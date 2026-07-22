<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $patients = Patient::withCount('appointments')
            ->when($request->filled('q'), function (Builder $query) use ($request) {
                $term = trim($request->string('q'));
                $query->where(function (Builder $q) use ($term) {
                    $q->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->orderBy('last_name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.patients.index', ['patients' => $patients]);
    }

    /**
     * Fiche patient synthétique : coordonnées, historique, notes internes.
     */
    public function show(Patient $patient): View
    {
        $patient->load([
            'appointments' => fn ($q) => $q->with(['doctor', 'specialty'])->orderByDesc('date')->orderByDesc('start_time'),
        ]);

        return view('admin.patients.show', ['patient' => $patient]);
    }

    /**
     * Mise à jour des notes internes (visibles uniquement du personnel).
     */
    public function updateNotes(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'internal_notes' => ['nullable', 'string', 'max:5000'],
        ], [], ['internal_notes' => 'notes internes']);

        $patient->update(['internal_notes' => $validated['internal_notes']]);

        ActivityLog::record('patient.notes_updated', $patient, 'Notes internes mises à jour par ' . auth()->user()->name);

        return back()->with('success', 'Notes internes enregistrées.');
    }
}
