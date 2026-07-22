@extends('layouts.admin')

@section('title', 'Rendez-vous')

@section('content')

    {{-- Filtres --}}
    <form method="GET" action="{{ route('admin.appointments.index') }}" class="card border-0 shadow-sm mb-4">
        <div class="card-body row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold" for="statut">Statut</label>
                <select class="form-select form-select-sm" id="statut" name="statut">
                    <option value="">Tous</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('statut') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if (auth()->user()->role !== \App\Enums\UserRole::Medecin)
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold" for="medecin">Médecin</label>
                    <select class="form-select form-select-sm" id="medecin" name="medecin">
                        <option value="">Tous</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected(request('medecin') == $doctor->id)>{{ $doctor->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold" for="specialite">Spécialité</label>
                    <select class="form-select form-select-sm" id="specialite" name="specialite">
                        <option value="">Toutes</option>
                        @foreach ($specialties as $specialty)
                            <option value="{{ $specialty->id }}" @selected(request('specialite') == $specialty->id)>{{ $specialty->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold" for="du">Du</label>
                <input type="date" class="form-control form-control-sm" id="du" name="du" value="{{ request('du') }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold" for="au">Au</label>
                <input type="date" class="form-control form-control-sm" id="au" name="au" value="{{ request('au') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold" for="q">Recherche</label>
                <input type="search" class="form-control form-control-sm" id="q" name="q"
                       value="{{ request('q') }}" placeholder="Nom, téléphone, code…">
            </div>
            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-secondary"><i class="bi bi-funnel me-1"></i>Filtrer</button>
                <a href="{{ route('admin.appointments.index') }}" class="btn btn-sm btn-outline-secondary">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">{{ $appointments->total() }} rendez-vous</h2>
        </div>
        <div class="card-body p-0">
            @if ($appointments->isEmpty())
                <p class="text-muted text-center py-4 mb-0">Aucun rendez-vous ne correspond à ces critères.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Date</th>
                                <th>Patient</th>
                                <th class="d-none d-lg-table-cell">Téléphone</th>
                                <th class="d-none d-md-table-cell">Médecin</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appointments as $appointment)
                                <tr>
                                    <td class="small font-monospace">{{ $appointment->tracking_code }}</td>
                                    <td class="small">
                                        <strong>{{ $appointment->date->format('d/m/Y') }}</strong><br>
                                        {{ substr($appointment->start_time, 0, 5) }}
                                    </td>
                                    <td>{{ $appointment->patient->full_name }}</td>
                                    <td class="d-none d-lg-table-cell small">{{ $appointment->patient->phone }}</td>
                                    <td class="d-none d-md-table-cell small">{{ $appointment->doctor->full_name }}</td>
                                    <td><span class="badge {{ $appointment->status->badgeClass() }}">{{ $appointment->status->label() }}</span></td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            @if ($appointment->isPending() && auth()->user()->role !== \App\Enums\UserRole::Medecin)
                                                <form method="POST" action="{{ route('admin.appointments.confirm', $appointment) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Confirmer">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.appointments.show', $appointment) }}"
                                               class="btn btn-sm btn-outline-secondary" title="Détails">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if ($appointments->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center">
                {{ $appointments->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

@endsection
