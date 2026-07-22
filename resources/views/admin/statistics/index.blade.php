@extends('layouts.admin')

@section('title', 'Statistiques')

@section('content')

    {{-- Période & exports --}}
    <x-admin.card title="Période analysée" icon="bi-calendar-range" class="mb-4">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <label for="du" class="form-label small">Du</label>
                <input type="date" class="form-control form-control-sm" id="du" name="du" value="{{ $from->toDateString() }}">
            </div>
            <div class="col-6 col-md-3">
                <label for="au" class="form-label small">Au</label>
                <input type="date" class="form-control form-control-sm" id="au" name="au" value="{{ $to->toDateString() }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-secondary w-100"><i class="bi bi-funnel me-1"></i>Analyser</button>
            </div>
            <div class="col-md-4 d-flex gap-2 justify-content-md-end">
                <a href="{{ route('admin.stats.excel', request()->query()) }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                </a>
                <a href="{{ route('admin.stats.pdf', request()->query()) }}" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Rapport PDF
                </a>
            </div>
        </form>
        <p class="small text-muted mb-0 mt-2">
            Du {{ $from->translatedFormat('d F Y') }} au {{ $to->translatedFormat('d F Y') }}
            ({{ $from->diffInDays($to) + 1 }} jours)
        </p>
    </x-admin.card>

    {{-- Indicateurs clés --}}
    <div class="row g-3 mb-4">
        @foreach ([
            ['label' => 'Total rendez-vous', 'value' => $figures['total'], 'icon' => 'bi-calendar2-week', 'class' => ''],
            ['label' => 'En attente', 'value' => $figures['pending'], 'icon' => 'bi-hourglass-split', 'class' => 'bg-warning-subtle text-warning'],
            ['label' => 'Confirmés', 'value' => $figures['confirmed'], 'icon' => 'bi-check2-circle', 'class' => 'bg-success-subtle text-success'],
            ['label' => 'Honorés', 'value' => $figures['completed'], 'icon' => 'bi-clipboard2-check', 'class' => 'bg-info-subtle text-info'],
            ['label' => 'Annulés', 'value' => $figures['cancelled'], 'icon' => 'bi-x-circle', 'class' => 'bg-danger-subtle text-danger'],
        ] as $card)
            <div class="col-6 col-lg">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $card['value'] }}</div>
                                <div class="stat-label">{{ $card['label'] }}</div>
                            </div>
                            <span class="icon-circle {{ $card['class'] }}" style="width:2.5rem;height:2.5rem;font-size:1.1rem;">
                                <i class="bi {{ $card['icon'] }}"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Taux --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card h-100"><div class="card-body">
                <div class="stat-value">{{ $figures['completion_rate'] }} %</div>
                <div class="stat-label">Taux d'honoration</div>
                <p class="small text-muted mb-0 mt-1">RDV honorés ÷ (honorés + annulés)</p>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card h-100"><div class="card-body">
                <div class="stat-value">{{ $figures['cancellation_rate'] }} %</div>
                <div class="stat-label">Taux d'annulation</div>
                <p class="small text-muted mb-0 mt-1">Sur l'ensemble des RDV</p>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card h-100"><div class="card-body">
                <div class="stat-value">{{ $figures['online_share'] }} %</div>
                <div class="stat-label">Pris en ligne</div>
                <p class="small text-muted mb-0 mt-1">Part du site web</p>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card h-100"><div class="card-body">
                <div class="stat-value">{{ $figures['new_patients'] }}</div>
                <div class="stat-label">Nouveaux patients</div>
                <p class="small text-muted mb-0 mt-1">Première consultation</p>
            </div></div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Taux de remplissage par médecin --}}
        <div class="col-lg-7">
            <x-admin.card title="Activité et taux de remplissage par médecin" icon="bi-person-badge">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Médecin</th>
                                <th class="text-center">RDV</th>
                                <th class="text-center d-none d-md-table-cell">Honorés</th>
                                <th class="text-center d-none d-md-table-cell">Annulés</th>
                                <th style="min-width:130px;">Remplissage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($byDoctor as $doctor)
                                <tr>
                                    <td>
                                        <strong class="small">{{ $doctor->full_name }}</strong>
                                        <div class="small text-muted">{{ $doctor->specialty->name }}</div>
                                    </td>
                                    <td class="text-center">{{ $doctor->total_count }}</td>
                                    <td class="text-center d-none d-md-table-cell">{{ $doctor->completed_count }}</td>
                                    <td class="text-center d-none d-md-table-cell">{{ $doctor->cancelled_count }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height:8px;">
                                                <div class="progress-bar bg-{{ $doctor->fill_rate >= 70 ? 'success' : ($doctor->fill_rate >= 35 ? 'warning' : 'danger') }}"
                                                     style="width: {{ min($doctor->fill_rate, 100) }}%"></div>
                                            </div>
                                            <span class="small text-muted" style="min-width:42px;">{{ $doctor->fill_rate }} %</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="small text-muted mb-0 mt-2">
                    Remplissage = rendez-vous occupés ÷ créneaux ouverts sur la période (absences déduites).
                </p>
            </x-admin.card>
        </div>

        {{-- Répartition par spécialité --}}
        <div class="col-lg-5">
            <x-admin.card title="Répartition par spécialité" icon="bi-pie-chart">
                @php($maxSpecialty = $bySpecialty->max('total') ?: 1)
                @forelse ($bySpecialty as $row)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ $row->name }}</span>
                            <strong>{{ $row->total }}</strong>
                        </div>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar" style="width: {{ round($row->total / $maxSpecialty * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">Aucune donnée sur cette période.</p>
                @endforelse
            </x-admin.card>
        </div>

        {{-- Pics d'affluence horaire --}}
        <div class="col-lg-7">
            <x-admin.card title="Pics d'affluence par créneau horaire" icon="bi-bar-chart">
                @php($maxHour = $byHour->max('total') ?: 1)
                @if ($byHour->isEmpty())
                    <p class="text-muted small mb-0">Aucune donnée sur cette période.</p>
                @else
                    <div class="d-flex align-items-end gap-2" style="height:180px;">
                        @foreach ($byHour as $row)
                            <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-end h-100">
                                <span class="small text-muted">{{ $row->total }}</span>
                                <div class="w-100 rounded-top bg-secondary"
                                     style="height: {{ max(round($row->total / $maxHour * 130), 4) }}px;"
                                     title="{{ $row->total }} RDV à {{ $row->hour }}h"></div>
                                <span class="small text-muted mt-1">{{ $row->hour }}h</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-admin.card>
        </div>

        {{-- Affluence par jour --}}
        <div class="col-lg-5">
            <x-admin.card title="Affluence par jour de la semaine" icon="bi-calendar-week">
                @php($maxWeekday = collect($byWeekday)->max('total') ?: 1)
                @foreach ($byWeekday as $row)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ $row['label'] }}</span>
                            <strong>{{ $row['total'] }}</strong>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-secondary" style="width: {{ round($row['total'] / $maxWeekday * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </x-admin.card>
        </div>

        {{-- Évolution quotidienne --}}
        <div class="col-12">
            <x-admin.card title="Évolution quotidienne du volume de rendez-vous" icon="bi-graph-up">
                @php($maxTrend = collect($trend)->max('total') ?: 1)
                <div class="d-flex align-items-end gap-1" style="height:150px;overflow-x:auto;">
                    @foreach ($trend as $day)
                        <div class="d-flex flex-column align-items-center justify-content-end h-100" style="min-width:26px;">
                            <div class="w-100 rounded-top" style="height: {{ max(round($day['total'] / $maxTrend * 110), 2) }}px; background: #a55a63;"
                                 title="{{ $day['date'] }} : {{ $day['total'] }} RDV"></div>
                            <span class="text-muted mt-1" style="font-size:0.65rem;">{{ $day['date'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-admin.card>
        </div>
    </div>
@endsection
