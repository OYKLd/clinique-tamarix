@extends('layouts.admin')

@section('title', 'Médecins')

@section('content')
    <x-admin.card title="Équipe médicale" icon="bi-person-badge">
        <x-slot:action>
            <a href="{{ route('admin.doctors.create') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-plus-lg me-1"></i>Ajouter un médecin
            </a>
        </x-slot:action>

        @if ($doctors->isEmpty())
            <p class="text-muted text-center py-4 mb-0">Aucun médecin enregistré.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th></th>
                            <th>Médecin</th>
                            <th class="d-none d-md-table-cell">Spécialité</th>
                            <th class="d-none d-lg-table-cell text-center">Disponibilités</th>
                            <th class="d-none d-lg-table-cell text-center">RDV</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($doctors as $doctor)
                            <tr>
                                <td style="width:52px;">
                                    @if ($doctor->photo)
                                        <img src="{{ asset('storage/' . $doctor->photo) }}" alt=""
                                             class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                                    @else
                                        <span class="icon-circle" style="width:40px;height:40px;font-size:1rem;">
                                            <i class="bi bi-person"></i>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $doctor->full_name }}</strong>
                                    @if ($doctor->user_id)
                                        <br><span class="badge text-bg-light small">Compte back-office</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">{{ $doctor->specialty->name }}</td>
                                <td class="d-none d-lg-table-cell text-center">{{ $doctor->availabilities_count }}</td>
                                <td class="d-none d-lg-table-cell text-center">{{ $doctor->appointments_count }}</td>
                                <td>
                                    <span class="badge {{ $doctor->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $doctor->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.doctors.edit', $doctor) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.doctors.destroy', $doctor) }}" class="d-inline"
                                          onsubmit="return confirm('Supprimer définitivement ce médecin ?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $doctors->links('pagination::bootstrap-5') }}</div>
        @endif
    </x-admin.card>
@endsection
