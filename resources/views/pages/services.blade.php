@extends('layouts.public')

@section('title', 'Nos Services & Spécialités — Clinique Tamarix Abidjan')
@section('meta_description', 'Spécialités médicales et chirurgicales de la Clinique Tamarix : médecine générale, gynécologie, pédiatrie, chirurgie, cardiologie, imagerie… Prise de rendez-vous en ligne.')

@section('content')
    <x-page-header title="Nos Services" subtitle="Spécialités médicales, chirurgicales et plateau technique" />

    {{-- Spécialités --}}
    <section class="section">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-eyebrow">Spécialités</span>
                <h2>Consultations médicales et chirurgicales</h2>
            </div>
            <div class="row g-4">
                @foreach ($specialties as $specialty)
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-hover h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex gap-3 mb-3">
                                    <span class="icon-circle"><i class="bi {{ $specialty->icon ?? 'bi-plus-circle' }}"></i></span>
                                    <div>
                                        <h3 class="h6 mb-1">{{ $specialty->name }}</h3>
                                        @if ($specialty->active_doctors_count > 0)
                                            <span class="small text-muted">
                                                {{ $specialty->active_doctors_count }}
                                                {{ Str::plural('médecin', $specialty->active_doctors_count) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <p class="small text-muted">{{ $specialty->description }}</p>
                                @if ($specialty->health_tip)
                                    <div class="health-tip small">
                                        <i class="bi bi-lightbulb me-1"></i>{{ $specialty->health_tip }}
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent border-0 pb-3">
                                <a href="{{ route('appointments.create', ['specialite' => $specialty->slug]) }}" class="fw-semibold small">
                                    Prendre rendez-vous <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Plateau technique --}}
    <section class="section bg-cream">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-eyebrow">Plateau technique</span>
                <h2>Tout, sur place, pour votre prise en charge</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card card-hover h-100 border-0 shadow-sm text-center">
                        <div class="card-body">
                            <span class="icon-circle mb-3"><i class="bi bi-camera2"></i></span>
                            <h3 class="h6">Imagerie</h3>
                            <p class="small text-muted mb-0">Radiographie et échographie de dernière génération.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card card-hover h-100 border-0 shadow-sm text-center">
                        <div class="card-body">
                            <span class="icon-circle mb-3"><i class="bi bi-droplet"></i></span>
                            <h3 class="h6">Laboratoire</h3>
                            <p class="small text-muted mb-0">Analyses médicales complètes, résultats rapides.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card card-hover h-100 border-0 shadow-sm text-center">
                        <div class="card-body">
                            <span class="icon-circle mb-3"><i class="bi bi-scissors"></i></span>
                            <h3 class="h6">Bloc opératoire</h3>
                            <p class="small text-muted mb-0">Chirurgie programmée et urgente en toute sécurité.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card card-hover h-100 border-0 shadow-sm text-center border-danger">
                        <div class="card-body">
                            <span class="icon-circle mb-3 bg-danger-subtle text-danger"><i class="bi bi-heart-pulse"></i></span>
                            <h3 class="h6">Urgences 24h/24</h3>
                            <p class="small text-muted mb-1">Jour et nuit, 7 jours sur 7.</p>
                            <a href="tel:{{ preg_replace('/\s+/', '', setting('emergency_phone', '')) }}" class="fw-bold small text-danger">
                                {{ setting('emergency_phone') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="section text-center">
        <div class="container">
            <h2 class="mb-3">Un besoin, une question de santé ?</h2>
            <p class="text-muted mb-4">Nos médecins vous reçoivent rapidement, sur rendez-vous en ligne.</p>
            <a href="{{ route('appointments.create') }}" class="btn-rdv btn-lg">
                <i class="bi bi-calendar2-heart me-2"></i>Prendre rendez-vous
            </a>
        </div>
    </section>
@endsection
