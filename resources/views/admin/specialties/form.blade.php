@extends('layouts.admin')

@section('title', $specialty->exists ? 'Modifier — ' . $specialty->name : 'Nouvelle spécialité')

@section('content')
    <x-admin.errors />

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <x-admin.card title="Spécialité" icon="bi-clipboard2-pulse">
                <form method="POST"
                      action="{{ $specialty->exists ? route('admin.specialties.update', $specialty) : route('admin.specialties.store') }}">
                    @csrf
                    @if ($specialty->exists) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="{{ old('name', $specialty->name) }}" required maxlength="120">
                        </div>
                        <div class="col-md-4">
                            <label for="sort_order" class="form-label">Ordre d'affichage</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order"
                                   value="{{ old('sort_order', $specialty->sort_order ?? 0) }}" min="0" max="999">
                        </div>

                        <div class="col-12">
                            <label for="icon" class="form-label">
                                Icône
                                <span class="text-muted small">(classe Bootstrap Icons, ex. <code>bi-heart-pulse</code>)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi {{ $specialty->icon ?? 'bi-plus-circle' }}" id="iconPreview"></i></span>
                                <input type="text" class="form-control" id="icon" name="icon"
                                       value="{{ old('icon', $specialty->icon) }}" maxlength="60" placeholder="bi-heart-pulse">
                            </div>
                            <div class="form-text">
                                Catalogue des icônes : <a href="https://icons.getbootstrap.com" target="_blank" rel="noopener">icons.getbootstrap.com</a>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Description <span class="text-muted small">(affichée sur le site)</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" maxlength="1000">{{ old('description', $specialty->description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label for="health_tip" class="form-label">
                                Conseil santé
                                <span class="text-muted small">(message de prévention affiché sur le site)</span>
                            </label>
                            <textarea class="form-control" id="health_tip" name="health_tip" rows="2" maxlength="500">{{ old('health_tip', $specialty->health_tip) }}</textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                       @checked(old('is_active', $specialty->is_active))>
                                <label class="form-check-label" for="is_active">
                                    Spécialité active <span class="text-muted small">(proposée à la réservation en ligne)</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-secondary">
                                <i class="bi bi-check2 me-1"></i>{{ $specialty->exists ? 'Enregistrer' : 'Créer' }}
                            </button>
                            <a href="{{ route('admin.specialties.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </div>
                </form>
            </x-admin.card>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('icon').addEventListener('input', function () {
        document.getElementById('iconPreview').className = 'bi ' + (this.value || 'bi-plus-circle');
    });
</script>
@endpush
