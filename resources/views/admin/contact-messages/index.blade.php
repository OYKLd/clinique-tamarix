@extends('layouts.admin')

@section('title', 'Messages de contact')

@section('content')
    <x-admin.card title="Messages reçus" icon="bi-envelope">
        @if ($messages->isEmpty())
            <p class="text-muted text-center py-4 mb-0">Aucun message reçu.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Expéditeur</th>
                            <th class="d-none d-md-table-cell">Objet</th>
                            <th class="d-none d-lg-table-cell">Reçu le</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messages as $message)
                            <tr class="{{ $message->read_at ? '' : 'fw-semibold' }}">
                                <td>
                                    {{ $message->name }}
                                    <div class="small text-muted fw-normal">{{ $message->phone ?: $message->email }}</div>
                                </td>
                                <td class="d-none d-md-table-cell">{{ Str::limit($message->subject ?: $message->message, 50) }}</td>
                                <td class="d-none d-lg-table-cell small text-muted fw-normal">{{ $message->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge {{ $message->read_at ? 'text-bg-light' : 'text-bg-warning' }}">
                                        {{ $message->read_at ? 'Lu' : 'Non lu' }}
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.contact-messages.show', $message) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $messages->links('pagination::bootstrap-5') }}</div>
        @endif
    </x-admin.card>
@endsection
