@extends('layouts.admin')

@section('title', 'Patients')

@section('content')

    <form method="GET" action="{{ route('admin.patients.index') }}" class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex gap-2">
            <input type="search" class="form-control" name="q" value="{{ request('q') }}"
                   placeholder="Rechercher par nom ou téléphone…">
            <button type="submit" class="btn btn-secondary"><i class="bi bi-search"></i></button>
            @if (request('q'))
                <a href="{{ route('admin.patients.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h2 class="h6 mb-0">{{ $patients->total() }} patient{{ $patients->total() > 1 ? 's' : '' }}</h2>
        </div>
        <div class="card-body p-0">
            @if ($patients->isEmpty())
                <p class="text-muted text-center py-4 mb-0">Aucun patient trouvé.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Téléphone</th>
                                <th class="d-none d-md-table-cell">Consentement notifications</th>
                                <th class="d-none d-md-table-cell">Rendez-vous</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patients as $patient)
                                <tr>
                                    <td class="fw-semibold">{{ $patient->full_name }}</td>
                                    <td class="small">{{ $patient->phone }}</td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="badge {{ $patient->whatsapp_consent ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $patient->whatsapp_consent ? 'Accepté' : 'Refusé' }}
                                        </span>
                                    </td>
                                    <td class="d-none d-md-table-cell small">{{ $patient->appointments_count }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if ($patients->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center">
                {{ $patients->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

@endsection
