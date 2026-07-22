@extends('layouts.admin')

@section('title', 'Journal des actions')

@section('content')
    <x-admin.card title="Filtrer le journal" icon="bi-funnel" class="mb-4">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="utilisateur" class="form-label small">Utilisateur</label>
                <select class="form-select form-select-sm" id="utilisateur" name="utilisateur">
                    <option value="">Tous</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('utilisateur') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="action" class="form-label small">Action</label>
                <select class="form-select form-select-sm" id="action" name="action">
                    <option value="">Toutes</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="du" class="form-label small">Du</label>
                <input type="date" class="form-control form-control-sm" id="du" name="du" value="{{ request('du') }}">
            </div>
            <div class="col-6 col-md-2">
                <label for="au" class="form-label small">Au</label>
                <input type="date" class="form-control form-control-sm" id="au" name="au" value="{{ request('au') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-sm btn-secondary flex-grow-1">Filtrer</button>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </x-admin.card>

    <x-admin.card title="{{ $logs->total() }} action(s) enregistrée(s)" icon="bi-clock-history">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th class="d-none d-md-table-cell">Détail</th>
                        <th class="d-none d-lg-table-cell">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td class="small text-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="small">{{ $log->user?->name ?? 'Patient (libre-service)' }}</td>
                            <td><code class="small">{{ $log->action }}</code></td>
                            <td class="d-none d-md-table-cell small text-muted">{{ $log->description }}</td>
                            <td class="d-none d-lg-table-cell small text-muted">{{ $log->ip_address }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $logs->links('pagination::bootstrap-5') }}</div>
    </x-admin.card>
@endsection
