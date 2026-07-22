@extends('layouts.admin')

@section('title', 'Notifications WhatsApp')

@section('content')

    @unless ($isLive)
        <div class="alert alert-warning">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Mode simulation.</strong> Les messages sont journalisés mais ne sont pas réellement envoyés.
            Renseignez les identifiants Meta dans le fichier <code>.env</code>
            (<code>WHATSAPP_DRIVER=cloud</code>) pour activer l'envoi réel.
        </div>
    @endunless

    <div class="row g-3 mb-4">
        @foreach (\App\Enums\NotificationStatus::cases() as $status)
            <div class="col-6 col-lg">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="stat-value">{{ $counts[$status->value] ?? 0 }}</div>
                        <div class="stat-label">{{ $status->label() }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <x-admin.card title="Journal des envois" icon="bi-whatsapp">
        <x-slot:action>
            <form method="GET" class="d-flex gap-2">
                <select name="statut" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    @foreach (\App\Enums\NotificationStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(request('statut') === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
                <select name="modele" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Tous les messages</option>
                    @foreach (['rdv_recu' => 'Demande reçue', 'rdv_confirme' => 'Confirmation', 'rappel_j1' => 'Rappel J-1', 'rdv_annule' => 'Annulation', 'rdv_reporte' => 'Report'] as $key => $label)
                        <option value="{{ $key }}" @selected(request('modele') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </x-slot:action>

        @if ($logs->isEmpty())
            <p class="text-muted text-center py-4 mb-0">Aucune notification enregistrée.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Destinataire</th>
                            <th>Message</th>
                            <th class="d-none d-md-table-cell">Canal</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td class="small text-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="small">
                                    {{ $log->patient?->full_name ?? '—' }}
                                    <div class="text-muted">{{ $log->recipient }}</div>
                                </td>
                                <td class="small">
                                    <span class="badge text-bg-light">{{ $log->template }}</span>
                                    @if ($log->appointment)
                                        <div class="text-muted">{{ $log->appointment->tracking_code }}</div>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell small">{{ $log->channel->label() }}</td>
                                <td>
                                    <span class="badge {{ $log->status->badgeClass() }}">{{ $log->status->label() }}</span>
                                    @if ($log->error)
                                        <div class="small text-danger">{{ Str::limit($log->error, 40) }}</div>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal" data-bs-target="#message{{ $log->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if ($log->status === \App\Enums\NotificationStatus::Failed)
                                        <form method="POST" action="{{ route('admin.notifications.retry', $log) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-primary" title="Relancer l'envoi">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $logs->links('pagination::bootstrap-5') }}</div>

            {{-- Aperçus des messages --}}
            @foreach ($logs as $log)
                <div class="modal fade" id="message{{ $log->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title h6">Message envoyé — {{ $log->template }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                            </div>
                            <div class="modal-body">
                                <div class="bg-light rounded-3 p-3 small" style="white-space:pre-line;">{{ $log->content }}</div>
                                <dl class="row small mt-3 mb-0">
                                    <dt class="col-5">Destinataire</dt><dd class="col-7">{{ $log->recipient }}</dd>
                                    @if ($log->sent_at)
                                        <dt class="col-5">Envoyé le</dt><dd class="col-7">{{ $log->sent_at->format('d/m/Y à H\hi') }}</dd>
                                    @endif
                                    @if ($log->delivered_at)
                                        <dt class="col-5">Délivré le</dt><dd class="col-7">{{ $log->delivered_at->format('d/m/Y à H\hi') }}</dd>
                                    @endif
                                    @if ($log->read_at)
                                        <dt class="col-5">Lu le</dt><dd class="col-7">{{ $log->read_at->format('d/m/Y à H\hi') }}</dd>
                                    @endif
                                    @if ($log->error)
                                        <dt class="col-5 text-danger">Erreur</dt><dd class="col-7 text-danger">{{ $log->error }}</dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </x-admin.card>
@endsection
