@extends('layouts.admin')

@section('title', 'Utilisateurs')

@section('content')
    <x-admin.card title="Comptes du personnel" icon="bi-person-gear">
        <x-slot:action>
            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-plus-lg me-1"></i>Nouveau compte
            </a>
        </x-slot:action>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th class="d-none d-md-table-cell">E-mail</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                @if ($user->id === auth()->id())
                                    <span class="badge text-bg-info ms-1">Vous</span>
                                @endif
                                <div class="d-md-none small text-muted">{{ $user->email }}</div>
                            </td>
                            <td class="d-none d-md-table-cell small text-muted">{{ $user->email }}</td>
                            <td><span class="badge text-bg-light">{{ $user->role->label() }}</span></td>
                            <td>
                                <span class="badge {{ $user->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $user->is_active ? 'Actif' : 'Désactivé' }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline"
                                          onsubmit="return confirm('Supprimer ce compte ?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $users->links('pagination::bootstrap-5') }}</div>
    </x-admin.card>
@endsection
