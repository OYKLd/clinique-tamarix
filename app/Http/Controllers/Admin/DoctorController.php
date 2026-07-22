<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\AvailabilityRequest;
use App\Http\Requests\DoctorRequest;
use App\Models\ActivityLog;
use App\Models\Absence;
use App\Models\Availability;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(): View
    {
        return view('admin.doctors.index', [
            'doctors' => Doctor::with('specialty')
                ->withCount(['appointments', 'availabilities'])
                ->orderBy('sort_order')
                ->orderBy('last_name')
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.doctors.form', [
            'doctor' => new Doctor(['is_active' => true, 'title' => 'Dr']),
            'specialties' => Specialty::ordered()->get(),
            'availableUsers' => $this->availableUsers(),
        ]);
    }

    public function store(DoctorRequest $request): RedirectResponse
    {
        $data = $this->prepare($request);

        $doctor = Doctor::create($data);

        ActivityLog::record('doctor.created', $doctor, "Médecin {$doctor->full_name} créé");

        return redirect()
            ->route('admin.doctors.edit', $doctor)
            ->with('success', "Le médecin {$doctor->full_name} a été créé. Définissez maintenant ses disponibilités.");
    }

    public function edit(Doctor $doctor): View
    {
        $doctor->load(['availabilities' => fn ($q) => $q->orderBy('weekday')->orderBy('start_time'), 'absences']);

        return view('admin.doctors.form', [
            'doctor' => $doctor,
            'specialties' => Specialty::ordered()->get(),
            'availableUsers' => $this->availableUsers($doctor),
        ]);
    }

    public function update(DoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        $data = $this->prepare($request, $doctor);

        $doctor->update($data);

        ActivityLog::record('doctor.updated', $doctor, "Fiche de {$doctor->full_name} modifiée");

        return back()->with('success', 'Fiche du médecin mise à jour.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        if ($doctor->appointments()->exists()) {
            return back()->with('error', 'Ce médecin a des rendez-vous enregistrés : désactivez sa fiche plutôt que de la supprimer.');
        }

        $name = $doctor->full_name;

        if ($doctor->photo) {
            Storage::disk('public')->delete($doctor->photo);
        }

        $doctor->delete();

        ActivityLog::record('doctor.deleted', null, "Médecin {$name} supprimé");

        return redirect()->route('admin.doctors.index')->with('success', "Le médecin {$name} a été supprimé.");
    }

    /**
     * Ajoute une plage de disponibilité hebdomadaire.
     */
    public function storeAvailability(AvailabilityRequest $request, Doctor $doctor): RedirectResponse
    {
        $doctor->availabilities()->create($request->validated());

        ActivityLog::record('availability.created', $doctor, "Disponibilité ajoutée pour {$doctor->full_name}");

        return back()->with('success', 'Disponibilité ajoutée.');
    }

    public function destroyAvailability(Doctor $doctor, Availability $availability): RedirectResponse
    {
        abort_unless($availability->doctor_id === $doctor->id, 404);

        $availability->delete();

        ActivityLog::record('availability.deleted', $doctor, "Disponibilité supprimée pour {$doctor->full_name}");

        return back()->with('success', 'Disponibilité supprimée.');
    }

    /**
     * Enregistre une absence (congés, garde, formation).
     */
    public function storeAbsence(Request $request, Doctor $doctor): RedirectResponse
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:150'],
        ], [], [
            'start_date' => 'date de début',
            'end_date' => 'date de fin',
            'reason' => 'motif',
        ]);

        $doctor->absences()->create($validated);

        ActivityLog::record('absence.created', $doctor, "Absence enregistrée pour {$doctor->full_name}");

        return back()->with('success', 'Absence enregistrée. Les créneaux concernés ne sont plus réservables.');
    }

    public function destroyAbsence(Doctor $doctor, Absence $absence): RedirectResponse
    {
        abort_unless($absence->doctor_id === $doctor->id, 404);

        $absence->delete();

        ActivityLog::record('absence.deleted', $doctor, "Absence supprimée pour {$doctor->full_name}");

        return back()->with('success', 'Absence supprimée.');
    }

    /**
     * Prépare les données : slug, photo, cases à cocher.
     */
    private function prepare(DoctorRequest $request, ?Doctor $doctor = null): array
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['slug'] = $this->uniqueSlug($data['first_name'], $data['last_name'], $doctor);

        if ($request->hasFile('photo')) {
            if ($doctor?->photo) {
                Storage::disk('public')->delete($doctor->photo);
            }
            $data['photo'] = $request->file('photo')->store('doctors', 'public');
        } else {
            unset($data['photo']);
        }

        return $data;
    }

    private function uniqueSlug(string $firstName, string $lastName, ?Doctor $doctor): string
    {
        $base = Str::slug("dr-{$firstName}-{$lastName}");
        $slug = $base;
        $suffix = 2;

        while (Doctor::where('slug', $slug)->when($doctor, fn ($q) => $q->where('id', '!=', $doctor->id))->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    /**
     * Comptes « médecin » non encore rattachés à une fiche.
     */
    private function availableUsers(?Doctor $doctor = null)
    {
        return User::where('role', UserRole::Medecin)
            ->where(function ($query) use ($doctor) {
                $query->whereDoesntHave('doctor');
                if ($doctor?->user_id) {
                    $query->orWhere('id', $doctor->user_id);
                }
            })
            ->orderBy('name')
            ->get();
    }
}
