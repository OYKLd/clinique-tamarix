@extends('layouts.admin')

@section('title', 'Paramètres de la clinique')

@section('content')
    <x-admin.errors />

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf @method('PUT')

                @foreach ($sections as $section => $fields)
                    <x-admin.card :title="$section" icon="bi-gear" class="mb-4">
                        <div class="row g-3">
                            @foreach ($fields as $key => $field)
                                <div class="col-md-{{ in_array($key, ['clinic_address', 'clinic_hours', 'maps_embed_url']) ? '12' : '6' }}">
                                    <label for="{{ $key }}" class="form-label">{{ $field['label'] }}</label>
                                    <input type="{{ str_contains($field['rules'], 'url') ? 'url' : (str_contains($field['rules'], 'email') ? 'email' : 'text') }}"
                                           class="form-control @error($key) is-invalid @enderror"
                                           id="{{ $key }}" name="{{ $key }}"
                                           value="{{ old($key, $values[$key] ?? '') }}"
                                           @if (str_contains($field['rules'], 'required')) required @endif>
                                    @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                        </div>
                    </x-admin.card>
                @endforeach

                <div class="d-flex gap-2 mb-4">
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-check2 me-1"></i>Enregistrer les paramètres
                    </button>
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Vérifier sur le site
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
