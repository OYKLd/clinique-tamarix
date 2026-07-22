@extends('layouts.admin')

@section('title', 'Message de ' . $message->name)

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left me-1"></i>Retour aux messages
            </a>

            <x-admin.card :title="$message->subject ?: 'Message sans objet'" icon="bi-envelope-open">
                <dl class="row small mb-4">
                    <dt class="col-sm-3">Expéditeur</dt>
                    <dd class="col-sm-9">{{ $message->name }}</dd>

                    @if ($message->phone)
                        <dt class="col-sm-3">Téléphone</dt>
                        <dd class="col-sm-9">
                            <a href="tel:{{ $message->phone }}">{{ $message->phone }}</a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $message->phone) }}" target="_blank" rel="noopener"
                               class="btn btn-sm btn-outline-success ms-2">
                                <i class="bi bi-whatsapp"></i> Répondre sur WhatsApp
                            </a>
                        </dd>
                    @endif

                    @if ($message->email)
                        <dt class="col-sm-3">E-mail</dt>
                        <dd class="col-sm-9"><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd>
                    @endif

                    <dt class="col-sm-3">Reçu le</dt>
                    <dd class="col-sm-9">{{ $message->created_at->format('d/m/Y à H\hi') }}</dd>
                </dl>

                <div class="bg-light rounded-3 p-3 mb-4" style="white-space: pre-line;">{{ $message->message }}</div>

                <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}"
                      onsubmit="return confirm('Supprimer définitivement ce message ?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash me-1"></i>Supprimer ce message
                    </button>
                </form>
            </x-admin.card>
        </div>
    </div>
@endsection
