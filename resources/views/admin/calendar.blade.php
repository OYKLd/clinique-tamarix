@extends('layouts.admin')

@section('title', 'Calendrier')

@section('content')

    {{-- Barre d'outils --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <div class="btn-group" role="group" aria-label="Navigation">
                <a href="{{ route('admin.calendar', array_filter(['vue' => $view, 'date' => $previous->toDateString(), 'medecin' => request('medecin')])) }}"
                   class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-left"></i></a>
                <a href="{{ route('admin.calendar', array_filter(['vue' => $view, 'medecin' => request('medecin')])) }}"
                   class="btn btn-sm btn-outline-secondary">Aujourd'hui</a>
                <a href="{{ route('admin.calendar', array_filter(['vue' => $view, 'date' => $next->toDateString(), 'medecin' => request('medecin')])) }}"
                   class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-right"></i></a>
            </div>

            <h2 class="h6 mb-0 text-center">
                @if ($view === 'jour')
                    {{ ucfirst($date->translatedFormat('l j F Y')) }}
                @elseif ($view === 'semaine')
                    Semaine du {{ $start->format('d/m') }} au {{ $end->format('d/m/Y') }}
                @else
                    {{ ucfirst($date->translatedFormat('F Y')) }}
                @endif
            </h2>

            <div class="d-flex gap-2 align-items-center">
                @if (auth()->user()->role !== \App\Enums\UserRole::Medecin)
                    <form method="GET" action="{{ route('admin.calendar') }}" class="d-flex gap-2">
                        <input type="hidden" name="vue" value="{{ $view }}">
                        <input type="hidden" name="date" value="{{ $date->toDateString() }}">
                        <select name="medecin" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Tous les médecins</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}" @selected(request('medecin') == $doctor->id)>{{ $doctor->full_name }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif
                <div class="btn-group" role="group" aria-label="Vue">
                    @foreach (['jour' => 'Jour', 'semaine' => 'Semaine', 'mois' => 'Mois'] as $value => $label)
                        <a href="{{ route('admin.calendar', array_filter(['vue' => $value, 'date' => $date->toDateString(), 'medecin' => request('medecin')])) }}"
                           class="btn btn-sm {{ $view === $value ? 'btn-secondary' : 'btn-outline-secondary' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if ($view === 'mois')
        {{-- Vue mois : grille --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table class="table table-bordered align-top mb-0" style="table-layout:fixed;min-width:840px;">
                        <thead class="table-light text-center small">
                            <tr>
                                @foreach (['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $day)
                                    <th>{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php($cursor = $start->copy()->startOfWeek())
                            @while ($cursor->lessThanOrEqualTo($end))
                                <tr>
                                    @for ($i = 0; $i < 7; $i++)
                                        @php($dayAppointments = $appointmentsByDay->get($cursor->toDateString(), collect()))
                                        <td class="{{ $cursor->month !== $date->month ? 'bg-light text-muted' : '' }} {{ $cursor->isToday() ? 'border-primary border-2' : '' }}" style="height:96px;">
                                            <a href="{{ route('admin.calendar', array_filter(['vue' => 'jour', 'date' => $cursor->toDateString(), 'medecin' => request('medecin')])) }}"
                                               class="small fw-semibold d-block mb-1 text-decoration-none">
                                                {{ $cursor->day }}
                                            </a>
                                            @if ($dayAppointments->isNotEmpty())
                                                @php($active = $dayAppointments->filter(fn ($a) => in_array($a->status, \App\Enums\AppointmentStatus::activeStatuses(), true))->count())
                                                <span class="badge text-bg-primary d-block mb-1">{{ $dayAppointments->count() }} RDV</span>
                                                @if ($active > 0)
                                                    <span class="small text-muted">{{ $active }} actif{{ $active > 1 ? 's' : '' }}</span>
                                                @endif
                                            @endif
                                        </td>
                                        @php($cursor->addDay())
                                    @endfor
                                </tr>
                            @endwhile
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @elseif ($view === 'semaine')
        {{-- Vue semaine : 7 colonnes --}}
        <div class="row g-2">
            @php($cursor = $start->copy())
            @while ($cursor->lessThanOrEqualTo($end))
                @php($dayAppointments = $appointmentsByDay->get($cursor->toDateString(), collect()))
                <div class="col-12 col-md-6 col-xl">
                    <div class="card border-0 shadow-sm h-100 {{ $cursor->isToday() ? 'border-top border-primary border-3' : '' }}">
                        <div class="card-header bg-white py-2 text-center">
                            <a href="{{ route('admin.calendar', array_filter(['vue' => 'jour', 'date' => $cursor->toDateString(), 'medecin' => request('medecin')])) }}"
                               class="small fw-semibold text-decoration-none">
                                {{ ucfirst($cursor->translatedFormat('D j/m')) }}
                            </a>
                        </div>
                        <div class="card-body p-2 d-grid gap-1">
                            @forelse ($dayAppointments as $appointment)
                                <a href="{{ route('admin.appointments.show', $appointment) }}"
                                   class="small text-decoration-none rounded p-1 px-2 d-block border-start border-3
                                          {{ $appointment->isPending() ? 'border-warning bg-warning-subtle' : '' }}
                                          {{ $appointment->isConfirmed() ? 'border-success bg-success-subtle' : '' }}
                                          {{ $appointment->status === \App\Enums\AppointmentStatus::Completed ? 'border-secondary bg-light text-muted' : '' }}
                                          {{ $appointment->status === \App\Enums\AppointmentStatus::Cancelled ? 'border-danger bg-danger-subtle text-decoration-line-through' : '' }}">
                                    <strong>{{ substr($appointment->start_time, 0, 5) }}</strong>
                                    {{ Str::limit($appointment->patient->full_name, 16) }}
                                </a>
                            @empty
                                <span class="text-muted small text-center py-2">—</span>
                            @endforelse
                        </div>
                    </div>
                </div>
                @php($cursor->addDay())
            @endwhile
        </div>

    @else
        {{-- Vue jour : liste horaire --}}
        @php($dayAppointments = $appointmentsByDay->get($date->toDateString(), collect()))
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @if ($dayAppointments->isEmpty())
                    <p class="text-muted text-center py-5 mb-0">Aucun rendez-vous ce jour.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:90px;">Heure</th>
                                    <th>Patient</th>
                                    <th class="d-none d-md-table-cell">Spécialité</th>
                                    <th class="d-none d-md-table-cell">Médecin</th>
                                    <th>Statut</th>
                                    <th class="text-end"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dayAppointments as $appointment)
                                    <tr>
                                        <td class="fw-semibold">{{ substr($appointment->start_time, 0, 5) }}</td>
                                        <td>{{ $appointment->patient->full_name }}</td>
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
    @endif

@endsection
