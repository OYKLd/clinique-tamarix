<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Services\AppointmentNotifier;
use App\Services\AvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly AppointmentNotifier $notifier,
    ) {}

    /**
     * Écran unique de prise de rendez-vous.
     * ?specialite=slug et ?medecin=slug pré-remplissent le parcours.
     */
    public function create(Request $request): View
    {
        $preselectedDoctor = null;

        if ($request->filled('medecin')) {
            $preselectedDoctor = Doctor::active()
                ->with('specialty')
                ->where('slug', $request->string('medecin'))
                ->first();
        }

        return view('appointments.create', [
            'specialties' => Specialty::active()->ordered()->has('activeDoctors')->get(),
            'preselectedSpecialty' => $preselectedDoctor?->specialty->slug
                ?? $request->string('specialite')->toString(),
            'preselectedDoctor' => $preselectedDoctor?->slug,
        ]);
    }

    /**
     * API — médecins actifs d'une spécialité.
     */
    public function doctors(Request $request): JsonResponse
    {
        $specialty = Specialty::active()->where('slug', $request->query('specialite'))->firstOrFail();

        return response()->json([
            'doctors' => $specialty->activeDoctors()->get()
                ->map(fn (Doctor $doctor) => [
                    'slug' => $doctor->slug,
                    'name' => $doctor->full_name,
                ]),
        ]);
    }

    /**
     * API — dates disponibles pour un médecin ou « premier disponible ».
     */
    public function dates(Request $request): JsonResponse
    {
        [$specialty, $doctor] = $this->resolveSelection($request);

        $dates = $doctor
            ? $this->availability->availableDatesForDoctor($doctor)
            : $this->availability->availableDatesForSpecialty($specialty);

        return response()->json(['dates' => $dates]);
    }

    /**
     * API — créneaux libres pour une date.
     */
    public function slots(Request $request): JsonResponse
    {
        [$specialty, $doctor] = $this->resolveSelection($request);

        $date = Carbon::parse($request->query('date'));

        $slots = $doctor
            ? $this->availability->slotsForDoctorOn($doctor, $date)
            : $this->availability->slotsForSpecialtyOn($specialty, $date);

        return response()->json([
            'slots' => $slots->map(fn (array $slot) => ['start' => $slot['start'], 'end' => $slot['end']]),
        ]);
    }

    /**
     * Enregistre la demande de rendez-vous (statut « en attente »).
     */
    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $specialty = Specialty::active()->where('slug', $validated['specialite'])->firstOrFail();
        $date = Carbon::parse($validated['date']);
        $time = $validated['heure'];

        $appointment = DB::transaction(function () use ($validated, $specialty, $date, $time) {
            $doctor = $this->resolveDoctorForSlot($specialty, $validated['medecin'], $date, $time);

            if (! $doctor) {
                throw ValidationException::withMessages([
                    'heure' => 'Ce créneau vient d\'être réservé. Merci d\'en choisir un autre.',
                ]);
            }

            // Verrou anti-doublon : le créneau ne doit pas être pris entre-temps
            $alreadyTaken = Appointment::where('doctor_id', $doctor->id)
                ->whereDate('date', $date)
                ->where('start_time', $time . ':00')
                ->whereIn('status', AppointmentStatus::activeStatuses())
                ->lockForUpdate()
                ->exists();

            if ($alreadyTaken) {
                throw ValidationException::withMessages([
                    'heure' => 'Ce créneau vient d\'être réservé. Merci d\'en choisir un autre.',
                ]);
            }

            $phone = Patient::normalizePhone($validated['phone']);

            $patient = Patient::firstOrCreate(
                ['phone' => $phone, 'first_name' => $validated['first_name'], 'last_name' => $validated['last_name']],
                ['whatsapp_consent' => true],
            );
            $patient->update(['whatsapp_consent' => true]);

            $slot = $this->availability->slotsForDoctorOn($doctor, $date)
                ->firstWhere('start', $time);

            return Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'specialty_id' => $specialty->id,
                'date' => $date,
                'start_time' => $time,
                'end_time' => $slot['end'],
                'status' => AppointmentStatus::Pending,
                'reason' => $validated['reason'] ?? null,
                'is_new_patient' => ! $patient->appointments()->where('status', AppointmentStatus::Completed)->exists(),
                'source' => 'online',
            ]);
        });

        ActivityLog::record('appointment.booked', $appointment, 'Rendez-vous pris en ligne par le patient');
        $this->notifier->booked($appointment);

        return redirect()->route('appointments.confirmation', $appointment->tracking_code);
    }

    /**
     * Écran de confirmation après la prise de rendez-vous.
     */
    public function confirmation(string $trackingCode): View
    {
        $appointment = Appointment::with(['doctor', 'specialty', 'patient'])
            ->where('tracking_code', $trackingCode)
            ->firstOrFail();

        return view('appointments.confirmation', ['appointment' => $appointment]);
    }

    /**
     * Résout la spécialité et, si précisé, le médecin depuis la requête.
     *
     * @return array{0: Specialty, 1: ?Doctor}
     */
    private function resolveSelection(Request $request): array
    {
        $specialty = Specialty::active()->where('slug', $request->query('specialite'))->firstOrFail();

        $doctor = null;
        if ($request->query('medecin') && $request->query('medecin') !== 'any') {
            $doctor = Doctor::active()
                ->where('slug', $request->query('medecin'))
                ->where('specialty_id', $specialty->id)
                ->firstOrFail();
        }

        return [$specialty, $doctor];
    }

    /**
     * Détermine le médecin qui assurera le créneau demandé.
     */
    private function resolveDoctorForSlot(Specialty $specialty, string $doctorSlug, Carbon $date, string $time): ?Doctor
    {
        if ($doctorSlug !== 'any') {
            $doctor = Doctor::active()
                ->where('slug', $doctorSlug)
                ->where('specialty_id', $specialty->id)
                ->first();

            return $doctor && $this->availability->slotsForDoctorOn($doctor, $date)->contains('start', $time)
                ? $doctor
                : null;
        }

        // « Premier disponible » : premier médecin de la spécialité libre à cette heure
        foreach ($specialty->activeDoctors()->get() as $doctor) {
            if ($this->availability->slotsForDoctorOn($doctor, $date)->contains('start', $time)) {
                return $doctor;
            }
        }

        return null;
    }
}
