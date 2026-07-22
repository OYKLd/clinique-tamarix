@extends('layouts.admin')

@section('title', 'Patient — ' . $patient->full_name)

@section('content')

    <div class="mb-3">
        <a href="{{ route('admin.patients.index') }}" class="small">
            <i class="bi bi-arrow-left me-1"></i>Retour aux patients
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            {{-- Coordonnées --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">{{ $patient->full_name }}</h2>
                    <ul class="list-unstyled small d-grid gap-2 mb-0">
                        <li><i class="bi bi-telephone me-2 text-muted"></i>{{ $patient->phone }}</li>
                        @if ($patient->email)
                            <li><i class="bi bi-envelope me-2 text-muted"></i>{{ $patient->email }}</li>
                        @endif
                        <li>
                            <i class="bi bi-whatsapp me-2 text-muted"></i>
                            Notifications :
                            <span class="badge {{ $patient->whatsapp_consent ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $patient->whatsapp_consent ? 'acceptées' : 'refusées' }}
                            </span>
                        </li>
                        <li><i class="bi bi-calendar3 me-2 text-muted"></i>Patient depuis le {{ $patient->created_at->format('d/m/Y') }}</li>
                    </ul>
                </div>
            </div>

            {{-- Notes internes --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h3 class="h6 mb-0"><i class="bi bi-journal-text me-2 text-secondary"></i>Notes internes</h3>
                </div>
                <form method="POST" action="{{ route('admin.patients.notes', $patient) }}" class="card-body">
                    @csrf
                    @method('PUT')
                    <textarea class="form-control form-control-sm mb-2" name="internal_notes" rows="5"
                              placeholder="Notes visibles uniquement par le personnel…">{{ old('internal_notes', $patient->internal_notes) }}</textarea>
                    <button type="submit" class="btn btn-sm btn-secondary w-100">Enregistrer les notes</button>
                </form>
            </div>
        </div>

        {{-- Historique des visites --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h3 class="h6 mb-0">
                        <i class="bi bi-clock-history me-2 text-secondary"></i>
                        Historique — {{ $patient->appointments->count() }} rendez-vous
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if ($patient->appointments->isEmpty())
                        <p class="text-muted text-center py-4 mb-0">Aucun rendez-vous enregistré.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Date</th>
                                        <th class="d-none d-md-table-cell">Spécialité</th>
                                        <th class="d-none d-md-table-cell">Médecin</th>
                                        <th>Statut</th>
                                        <th class="text-end"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($patient->appointments as $appointment)
                                        <tr>
                                            <td class="small font-monospace">{{ $appointment->tracking_code }}</td>
                                            <td class="small">{{ $appointment->date->format('d/m/Y') }} {{ substr($appointment->start_time, 0, 5) }}</td>
                                            <td class="d-none d-md-table-cell small">{{ $appointment->specialty->name }}</td>
                                            <td class="d-none d-md-table-cell small">{{ $appointment->doctor->full_name }}</td>
                                            <td><span class="badge {{ $appointment->status->badgeClass() }}">{{ $appointment->status->label() }}</span></td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.appointments.show', $appointment) }}" class="btn btn-sm btn-outline-secondary">
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
            </div>
        </div>
    </div>

@endsection
