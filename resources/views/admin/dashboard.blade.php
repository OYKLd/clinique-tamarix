@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-value">{{ $pendingCount }}</div>
                            <div class="stat-label">En attente de confirmation</div>
                        </div>
                        <span class="icon-circle bg-warning-subtle text-warning"><i class="bi bi-hourglass-split"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-value">{{ $todayCount }}</div>
                            <div class="stat-label">Rendez-vous aujourd'hui</div>
                        </div>
                        <span class="icon-circle"><i class="bi bi-calendar2-day"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-value">{{ $upcomingCount }}</div>
                            <div class="stat-label">Rendez-vous à venir</div>
                        </div>
                        <span class="icon-circle bg-success-subtle text-success"><i class="bi bi-calendar2-week"></i></span>
                    </div>
                </div>
            </div>
        </div>
        @if (! is_null($unreadMessages))
            <div class="col-6 col-lg-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $unreadMessages }}</div>
                                <div class="stat-label">Messages non lus</div>
                            </div>
                            <span class="icon-circle bg-info-subtle text-info"><i class="bi bi-envelope"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0"><i class="bi bi-calendar2-day me-2 text-secondary"></i>Rendez-vous du jour</h2>
            @if (Route::has('admin.appointments.index'))
                <a href="{{ route('admin.appointments.index') }}" class="small">Tout voir</a>
            @endif
        </div>
        <div class="card-body p-0">
            @if ($todayAppointments->isEmpty())
                <p class="text-muted text-center py-4 mb-0">Aucun rendez-vous actif aujourd'hui.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Heure</th>
                                <th>Patient</th>
                                <th class="d-none d-md-table-cell">Spécialité</th>
                                <th class="d-none d-md-table-cell">Médecin</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($todayAppointments as $appointment)
                                <tr>
                                    <td class="fw-semibold">{{ substr($appointment->start_time, 0, 5) }}</td>
                                    <td>{{ $appointment->patient->full_name }}</td>
                                    <td class="d-none d-md-table-cell">{{ $appointment->specialty->name }}</td>
                                    <td class="d-none d-md-table-cell">{{ $appointment->doctor->full_name }}</td>
                                    <td><span class="badge {{ $appointment->status->badgeClass() }}">{{ $appointment->status->label() }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

@endsection
