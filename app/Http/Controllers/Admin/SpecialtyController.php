<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecialtyRequest;
use App\Models\ActivityLog;
use App\Models\Specialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SpecialtyController extends Controller
{
    public function index(): View
    {
        return view('admin.specialties.index', [
            'specialties' => Specialty::withCount('doctors')->ordered()->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('admin.specialties.form', [
            'specialty' => new Specialty(['is_active' => true]),
        ]);
    }

    public function store(SpecialtyRequest $request): RedirectResponse
    {
        $specialty = Specialty::create($this->prepare($request));

        ActivityLog::record('specialty.created', $specialty, "Spécialité « {$specialty->name} » créée");

        return redirect()->route('admin.specialties.index')
            ->with('success', "La spécialité « {$specialty->name} » a été créée.");
    }

    public function edit(Specialty $specialty): View
    {
        return view('admin.specialties.form', ['specialty' => $specialty]);
    }

    public function update(SpecialtyRequest $request, Specialty $specialty): RedirectResponse
    {
        $specialty->update($this->prepare($request, $specialty));

        ActivityLog::record('specialty.updated', $specialty, "Spécialité « {$specialty->name} » modifiée");

        return redirect()->route('admin.specialties.index')
            ->with('success', 'Spécialité mise à jour.');
    }

    public function destroy(Specialty $specialty): RedirectResponse
    {
        if ($specialty->doctors()->exists() || $specialty->appointments()->exists()) {
            return back()->with('error', 'Cette spécialité est utilisée par des médecins ou des rendez-vous : désactivez-la plutôt que de la supprimer.');
        }

        $name = $specialty->name;
        $specialty->delete();

        ActivityLog::record('specialty.deleted', null, "Spécialité « {$name} » supprimée");

        return back()->with('success', "La spécialité « {$name} » a été supprimée.");
    }

    private function prepare(SpecialtyRequest $request, ?Specialty $specialty = null): array
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $base = Str::slug($data['name']);
        $slug = $base;
        $suffix = 2;

        while (Specialty::where('slug', $slug)->when($specialty, fn ($q) => $q->where('id', '!=', $specialty->id))->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        $data['slug'] = $slug;

        return $data;
    }
}
