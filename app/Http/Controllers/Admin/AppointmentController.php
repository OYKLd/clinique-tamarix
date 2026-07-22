<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Services\AppointmentNotifier;
use App\Services\AvailabilityService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly AppointmentNotifier $notifier,
        private readonly AvailabilityService $availability,
    ) {}

    /**
     * Liste des rendez-vous, filtrable par statut, médecin, spécialité,
     * période et recherche libre (patient, téléphone, code de suivi).
     */
    public function index(Request $request): View
    {
        $appointments = $this->scopedQuery($request)
            ->with(['patient', 'doctor', 'specialty'])
            ->when($request->filled('statut'), fn (Builder $q) => $q->where('status', $request->string('statut')))
            ->when($request->filled('medecin'), fn (Builder $q) => $q->where('doctor_id', $request->integer('medecin')))
            ->when($request->filled('specialite'), fn (Builder $q) => $q->where('specialty_id', $request->integer('specialite')))
            ->when($request->filled('du'), fn (Builder $q) => $q->whereDate('date', '>=', $request->date('du')))
            ->when($request->filled('au'), fn (Builder $q) => $q->whereDate('date', '<=', $request->date('au')))
            ->when($request->filled('q'), function (Builder $query) use ($request) {
                $term = trim($request->string('q'));
                $query->where(function (Builder $q) use ($term) {
                    $q->where('tracking_code', 'like', "%{$term}%")
                        ->orWhereHas('patient', function (Builder $p) use ($term) {
                            $p->where('first_name', 'like', "%{$term}%")
                                ->orWhere('last_name', 'like', "%{$term}%")
                                ->orWhere('phone', 'like', "%{$term}%");
                        });
                });
            })
            ->orderByDesc('date')
            ->orderBy('start_time')
            ->paginate(20)
            ->withQueryString();

        return view('admin.appointments.index', [
            'appointments' => $appointments,
            'doctors' => Doctor::orderBy('last_name')->get(),
            'specialties' => Specialty::ordered()->get(),
            'statuses' => AppointmentStatus::options(),
        ]);
    }

    public function show(Request $request, Appointment $appointment): View
    {
        $this->authorizeView($request, $appointment);

        $appointment->load(['patient.appointments' => fn ($q) => $q->orderByDesc('date'), 'doctor', 'specialty', 'notificationLogs']);

        return view('admin.appointments.show', [
            'appointment' => $appointment,
            'activityLogs' => ActivityLog::with('user')
                ->where('subject_type', $appointment->getMorphClass())
                ->where('subject_id', $appointment->id)
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Validation du rendez-vous par l'accueil → notification de confirmation.
     */
    public function confirm(Appointment $appointment): RedirectResponse
    {
        if (! $appointment->isPending()) {
            return back()->with('error', 'Seul un rendez-vous en attente peut être confirmé.');
        }

        $appointment->update([
            'status' => AppointmentStatus::Confirmed,
            'confirmed_at' => now(),
        ]);

        ActivityLog::record('appointment.confirmed', $appointment, 'Rendez-vous confirmé par ' . auth()->user()->name);
        $this->notifier->confirmed($appointment);

        return back()->with('success', "Rendez-vous {$appointment->tracking_code} confirmé. Le patient est notifié.");
    }

    /**
     * Annulation par la clinique → créneau libéré + notification.
     */
    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        if (! in_array($appointment->status, AppointmentStatus::activeStatuses(), true)) {
            return back()->with('error', 'Ce rendez-vous ne peut plus être annulé.');
        }

        $validated = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:255'],
        ], [], ['cancellation_reason' => 'motif d\'annulation']);

        $appointment->update([
            'status' => AppointmentStatus::Cancelled,
            'cancelled_by' => 'clinic',
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'] ?? 'Annulé par la clinique',
        ]);

        ActivityLog::record('appointment.cancelled', $appointment, 'Annulation par ' . auth()->user()->name);
        $this->notifier->cancelled($appointment);

        return back()->with('success', "Rendez-vous {$appointment->tracking_code} annulé. Le créneau est libéré et le patient notifié.");
    }

    /**
     * Marque la consultation comme honorée.
     */
    public function complete(Appointment $appointment): RedirectResponse
    {
        if (! in_array($appointment->status, AppointmentStatus::activeStatuses(), true)) {
            return back()->with('error', 'Ce rendez-vous ne peut pas être marqué comme honoré.');
        }

        $appointment->update([
            'status' => AppointmentStatus::Completed,
            'completed_at' => now(),
        ]);

        ActivityLog::record('appointment.completed', $appointment, 'Consultation honorée — enregistré par ' . auth()->user()->name);

        return back()->with('success', "Rendez-vous {$appointment->tracking_code} marqué comme honoré.");
    }

    /**
     * Report du rendez-vous sur un nouveau créneau libre du même médecin.
     */
    public function reschedule(Request $request, Appointment $appointment): RedirectResponse
    {
        if (! in_array($appointment->status, AppointmentStatus::activeStatuses(), true)) {
            return back()->with('error', 'Ce rendez-vous ne peut plus être reporté.');
        }

        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'heure' => ['required', 'date_format:H:i'],
        ], [], ['date' => 'date', 'heure' => 'créneau']);

        $date = Carbon::parse($validated['date']);
        $slot = $this->availability->slotsForDoctorOn($appointment->doctor, $date)
            ->firstWhere('start', $validated['heure']);

        if (! $slot) {
            return back()->with('error', 'Ce créneau n\'est pas disponible pour ce médecin.');
        }

        $previous = $appointment->date->format('d/m/Y') . ' ' . substr($appointment->start_time, 0, 5);

        $appointment->update([
            'date' => $date,
            'start_time' => $slot['start'],
            'end_time' => $slot['end'],
        ]);

        ActivityLog::record(
            'appointment.rescheduled',
            $appointment,
            "Reporté du {$previous} au {$date->format('d/m/Y')} {$slot['start']} par " . auth()->user()->name,
        );
        $this->notifier->rescheduled($appointment);

        return back()->with('success', 'Rendez-vous reporté. Le patient est notifié du nouveau créneau.');
    }

    /**
     * Un médecin ne consulte que ses propres rendez-vous.
     */
    private function scopedQuery(Request $request): Builder
    {
        $user = $request->user();
        $query = Appointment::query();

        if ($user->role === UserRole::Medecin) {
            $query->where('doctor_id', $user->doctor?->id ?? 0);
        }

        return $query;
    }

    private function authorizeView(Request $request, Appointment $appointment): void
    {
        $user = $request->user();

        if ($user->role === UserRole::Medecin && $appointment->doctor_id !== $user->doctor?->id) {
            abort(403);
        }
    }
}
