@extends('layouts.admin')

@section('title', 'Rendez-vous ' . $appointment->tracking_code)

@php($isStaff = auth()->user()->role !== \App\Enums\UserRole::Medecin)
@php($isActive = in_array($appointment->status, \App\Enums\AppointmentStatus::activeStatuses(), true))

@section('content')

    <div class="mb-3">
        <a href="{{ route('admin.appointments.index') }}" class="small">
            <i class="bi bi-arrow-left me-1"></i>Retour à la liste
        </a>
    </div>

    <div class="row g-4">
        {{-- Détails du rendez-vous --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0">
                        <span class="font-monospace">{{ $appointment->tracking_code }}</span>
                    </h2>
                    <span class="badge fs-6 {{ $appointment->status->badgeClass() }}">{{ $appointment->status->label() }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3 small">
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Date et heure</span>
                            <strong>{{ ucfirst($appointment->date->translatedFormat('l j F Y')) }}</strong>
                            à {{ substr($appointment->start_time, 0, 5) }}
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Médecin</span>
                            <strong>{{ $appointment->doctor->full_name }}</strong> — {{ $appointment->specialty->name }}
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Source</span>
                            {{ $appointment->source === 'online' ? 'Site web' : 'Accueil' }}
                            · {{ $appointment->is_new_patient ? 'Nouveau patient' : 'Patient suivi' }}
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Motif indiqué</span>
                            {{ $appointment->reason ?: '—' }}
                        </div>
                        @if ($appointment->cancelled_at)
                            <div class="col-12">
                                <span class="text-muted d-block">Annulation</span>
                                Le {{ $appointment->cancelled_at->format('d/m/Y à H\hi') }}
                                par {{ $appointment->cancelled_by === 'patient' ? 'le patient' : 'la clinique' }}
                                — {{ $appointment->cancellation_reason ?: 'sans motif' }}
                            </div>
                        @endif
                    </div>
                </div>

                @if ($isStaff && $isActive)
                    <div class="card-footer bg-white py-3 d-flex flex-wrap gap-2">
                        @if ($appointment->isPending())
                            <form method="POST" action="{{ route('admin.appointments.confirm', $appointment) }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-check-lg me-1"></i>Confirmer
                                </button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.appointments.complete', $appointment) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-check2-all me-1"></i>Marquer honoré
                            </button>
                        </form>
                        <button class="btn btn-outline-primary btn-sm" type="button"
                                data-bs-toggle="collapse" data-bs-target="#rescheduleForm">
                            <i class="bi bi-arrow-repeat me-1"></i>Reporter
                        </button>
                        <button class="btn btn-outline-danger btn-sm" type="button"
                                data-bs-toggle="collapse" data-bs-target="#cancelForm">
                            <i class="bi bi-x-lg me-1"></i>Annuler
                        </button>
                    </div>

                    {{-- Report --}}
                    <div class="collapse border-top" id="rescheduleForm">
                        <form method="POST" action="{{ route('admin.appointments.reschedule', $appointment) }}" class="card-body row g-2 align-items-end">
                            @csrf
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold" for="newDate">Nouvelle date</label>
                                <select class="form-select form-select-sm" id="newDate" name="date" required
                                        data-doctor="{{ $appointment->doctor->slug }}"
                                        data-specialty="{{ $appointment->specialty->slug }}">
                                    <option value="" selected disabled>Chargement des disponibilités…</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold" for="newTime">Nouveau créneau</label>
                                <select class="form-select form-select-sm" id="newTime" name="heure" required disabled>
                                    <option value="" selected disabled>Choisissez une date</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Reporter le RDV</button>
                            </div>
                        </form>
                    </div>

                    {{-- Annulation --}}
                    <div class="collapse border-top" id="cancelForm">
                        <form method="POST" action="{{ route('admin.appointments.cancel', $appointment) }}" class="card-body row g-2 align-items-end"
                              onsubmit="return confirm('Confirmez-vous l\'annulation de ce rendez-vous ?');">
                            @csrf
                            <div class="col-md-9">
                                <label class="form-label small fw-semibold" for="cancelReason">Motif (transmis en interne)</label>
                                <input type="text" class="form-control form-control-sm" id="cancelReason"
                                       name="cancellation_reason" maxlength="255" placeholder="Ex. : médecin indisponible">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-danger btn-sm w-100">Annuler le RDV</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Journal des notifications --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-0"><i class="bi bi-whatsapp me-2 text-success"></i>Notifications envoyées</h2>
                </div>
                <div class="card-body p-0">
                    @if ($appointment->notificationLogs->isEmpty())
                        <p class="text-muted text-center py-3 mb-0 small">Aucune notification pour ce rendez-vous.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr><th>Date</th><th>Modèle</th><th>Canal</th><th>Statut</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($appointment->notificationLogs as $log)
                                        <tr>
                                            <td class="small">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="small font-monospace">{{ $log->template }}</td>
                                            <td class="small">{{ $log->channel->label() }}</td>
                                            <td><span class="badge {{ $log->status->badgeClass() }}">{{ $log->status->label() }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Journal d'audit --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-0"><i class="bi bi-clock-history me-2 text-secondary"></i>Historique des actions</h2>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse ($activityLogs as $log)
                        <li class="list-group-item small d-flex justify-content-between gap-2">
                            <span>{{ $log->description ?: $log->action }}</span>
                            <span class="text-muted text-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                    @empty
                        <li class="list-group-item small text-muted text-center">Aucune action enregistrée.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Fiche patient --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0"><i class="bi bi-person me-2 text-secondary"></i>Patient</h2>
                    @if ($isStaff && Route::has('admin.patients.show'))
                        <a href="{{ route('admin.patients.show', $appointment->patient) }}" class="small">Fiche complète</a>
                    @endif
                </div>
                <div class="card-body small">
                    <p class="fs-6 fw-semibold mb-1">{{ $appointment->patient->full_name }}</p>
                    <p class="mb-1"><i class="bi bi-telephone me-2 text-muted"></i>{{ $appointment->patient->phone }}</p>
                    <p class="mb-3">
                        <i class="bi bi-whatsapp me-2 text-muted"></i>
                        Notifications : {{ $appointment->patient->whatsapp_consent ? 'acceptées' : 'refusées' }}
                    </p>

                    <span class="text-muted d-block mb-2">Historique des rendez-vous</span>
                    <ul class="list-group list-group-flush">
                        @foreach ($appointment->patient->appointments->take(6) as $visit)
                            <li class="list-group-item px-0 d-flex justify-content-between gap-2">
                                <span>
                                    {{ $visit->date->format('d/m/Y') }} {{ substr($visit->start_time, 0, 5) }}
                                    <span class="text-muted">— {{ $visit->specialty->name }}</span>
                                </span>
                                <span class="badge {{ $visit->status->badgeClass() }}">{{ $visit->status->label() }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
@if ($isStaff && $isActive)
<script>
(function () {
    const dateSelect = document.getElementById('newDate');
    const timeSelect = document.getElementById('newTime');
    if (!dateSelect) return;

    const params = {
        specialite: dateSelect.dataset.specialty,
        medecin: dateSelect.dataset.doctor,
    };

    async function fetchJson(url, extra) {
        const query = new URLSearchParams({ ...params, ...extra }).toString();
        const response = await fetch(url + '?' + query, { headers: { Accept: 'application/json' } });
        return response.json();
    }

    document.querySelector('[data-bs-target="#rescheduleForm"]').addEventListener('click', async () => {
        if (dateSelect.options.length > 1) return;
        const data = await fetchJson(@json(route('booking.dates')), {});
        dateSelect.innerHTML = '<option value="" selected disabled>Choisissez une date…</option>';
        data.dates.forEach(d => dateSelect.add(new Option(d.label, d.date)));
    }, { once: false });

    dateSelect.addEventListener('change', async () => {
        timeSelect.innerHTML = '<option value="" selected disabled>Chargement…</option>';
        timeSelect.disabled = true;
        const data = await fetchJson(@json(route('booking.slots')), { date: dateSelect.value });
        timeSelect.innerHTML = '<option value="" selected disabled>Choisissez un créneau…</option>';
        data.slots.forEach(s => timeSelect.add(new Option(s.start + ' – ' + s.end, s.start)));
        timeSelect.disabled = false;
    });
})();
</script>
@endif
@endpush
