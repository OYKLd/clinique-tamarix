@extends('layouts.admin')

@section('title', 'Spécialités')

@section('content')
    <x-admin.card title="Spécialités médicales" icon="bi-clipboard2-pulse">
        <x-slot:action>
            <a href="{{ route('admin.specialties.create') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-plus-lg me-1"></i>Ajouter une spécialité
            </a>
        </x-slot:action>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Spécialité</th>
                        <th class="d-none d-md-table-cell">Conseil santé</th>
                        <th class="text-center">Médecins</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($specialties as $specialty)
                        <tr>
                            <td>
                                <i class="bi {{ $specialty->icon ?? 'bi-plus-circle' }} me-2 text-secondary"></i>
                                <strong>{{ $specialty->name }}</strong>
                            </td>
                            <td class="d-none d-md-table-cell small text-muted">
                                {{ $specialty->health_tip ? Str::limit($specialty->health_tip, 70) : '—' }}
                            </td>
                            <td class="text-center">{{ $specialty->doctors_count }}</td>
                            <td>
                                <span class="badge {{ $specialty->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $specialty->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.specialties.edit', $specialty) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.specialties.destroy', $specialty) }}" class="d-inline"
                                      onsubmit="return confirm('Supprimer cette spécialité ?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $specialties->links('pagination::bootstrap-5') }}</div>
    </x-admin.card>
@endsection
