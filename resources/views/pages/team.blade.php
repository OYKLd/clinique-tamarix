@extends('layouts.public')

@section('title', 'Notre Équipe médicale — Clinique Tamarix Abidjan')
@section('meta_description', 'Rencontrez les médecins de la Clinique Tamarix à Abidjan : généralistes, gynécologues, pédiatres, chirurgiens, cardiologues… Filtrez par spécialité et prenez rendez-vous.')

@section('content')
    <x-page-header title="Notre Équipe médicale" subtitle="Des praticiens expérimentés, à votre écoute" />

    <section class="section">
        <div class="container">

            {{-- Filtre par spécialité --}}
            <form method="GET" action="{{ route('team') }}" class="row justify-content-center mb-5">
                <div class="col-md-5 col-lg-4">
                    <label for="specialite" class="form-label small fw-semibold">Filtrer par spécialité</label>
                    <div class="input-group">
                        <select name="specialite" id="specialite" class="form-select" onchange="this.form.submit()">
                            <option value="">Toutes les spécialités</option>
                            @foreach ($specialties as $specialty)
                                <option value="{{ $specialty->slug }}" @selected($currentSpecialty === $specialty->slug)>
                                    {{ $specialty->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($currentSpecialty)
                            <a href="{{ route('team') }}" class="btn btn-outline-secondary" title="Réinitialiser le filtre">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if ($doctors->isEmpty())
                <p class="text-center text-muted">Aucun médecin trouvé pour cette spécialité.</p>
            @else
                <div class="row g-4">
                    @foreach ($doctors as $doctor)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card card-hover h-100 border-0 shadow-sm overflow-hidden">
                                @if ($doctor->photo)
                                    <img src="{{ asset('storage/' . $doctor->photo) }}" alt="{{ $doctor->full_name }}" class="doctor-photo">
                                @else
                                    <div class="doctor-photo-placeholder"><i class="bi bi-person"></i></div>
                                @endif
                                <div class="card-body text-center">
                                    <h2 class="h6 mb-1">{{ $doctor->full_name }}</h2>
                                    <p class="small text-primary mb-2">{{ $doctor->specialty->name }}</p>
                                    @if ($doctor->bio)
                                        <p class="small text-muted d-none d-md-block">{{ Str::limit($doctor->bio, 90) }}</p>
                                    @endif
                                    <a href="{{ route('appointments.create', ['medecin' => $doctor->slug]) }}"
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-1">
                                        Prendre rendez-vous
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
