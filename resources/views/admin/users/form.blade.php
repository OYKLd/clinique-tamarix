@extends('layouts.admin')

@section('title', $user->exists ? 'Modifier — ' . $user->name : 'Nouveau compte')

@section('content')
    <x-admin.errors />

    <div class="row">
        <div class="col-lg-7 mx-auto">
            <x-admin.card title="Compte utilisateur" icon="bi-person-gear">
                <form method="POST"
                      action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
                    @csrf
                    @if ($user->exists) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nom complet</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="{{ old('name', $user->name) }}" required maxlength="120">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="{{ old('email', $user->email) }}" required maxlength="150">
                        </div>

                        <div class="col-md-6">
                            <label for="role" class="form-label">Rôle</label>
                            <select class="form-select" id="role" name="role" required
                                    @disabled($user->exists && $user->id === auth()->id())>
                                @foreach (\App\Enums\UserRole::cases() as $role)
                                    <option value="{{ $role->value }}" @selected(old('role', $user->role?->value) === $role->value)>
                                        {{ $role->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($user->exists && $user->id === auth()->id())
                                <div class="form-text">Vous ne pouvez pas modifier votre propre rôle.</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Téléphone <span class="text-muted small">(facultatif)</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                   value="{{ old('phone', $user->phone) }}" maxlength="30">
                        </div>

                        <div class="col-12"><hr class="my-2"></div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">
                                Mot de passe
                                @if ($user->exists)<span class="text-muted small">(laisser vide pour conserver)</span>@endif
                            </label>
                            <input type="password" class="form-control" id="password" name="password"
                                   autocomplete="new-password" @required(! $user->exists)>
                            <div class="form-text">10 caractères minimum, avec lettres et chiffres.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                   autocomplete="new-password" @required(! $user->exists)>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                       @checked(old('is_active', $user->is_active))
                                       @disabled($user->exists && $user->id === auth()->id())>
                                <label class="form-check-label" for="is_active">
                                    Compte actif <span class="text-muted small">(un compte désactivé ne peut plus se connecter)</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-secondary">
                                <i class="bi bi-check2 me-1"></i>{{ $user->exists ? 'Enregistrer' : 'Créer le compte' }}
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </div>
                </form>
            </x-admin.card>
        </div>
    </div>
@endsection
