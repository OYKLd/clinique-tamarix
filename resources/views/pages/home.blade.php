@extends('layouts.public')

@section('title', 'Clinique Médico-Chirurgicale Tamarix — Abidjan | Prise de rendez-vous en ligne')

@section('content')

    {{-- Héros sur photo de la clinique --}}
    <section class="hero"
             style="background: linear-gradient(100deg, rgba(21, 62, 96, 0.93) 0%, rgba(29, 83, 127, 0.82) 45%, rgba(101, 51, 59, 0.55) 100%), url('{{ asset('images/hero.webp') }}') center 35% / cover no-repeat;">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <p class="hero-slogan fs-5 mb-2">« Nous plantons l'Espérance »</p>
                    <h1 class="fw-bold mb-3">
                        Votre santé mérite une prise en charge moderne, rigoureuse et humaine.
                    </h1>
                    <p class="lead mb-4 opacity-75">
                        Consultations spécialisées, chirurgie, imagerie et urgences 24h/24 à Abidjan.
                        Prenez rendez-vous en ligne en moins d'une minute.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('appointments.create') }}" class="btn-rdv btn-lg">
                            <i class="bi bi-calendar2-heart me-2"></i>Prendre rendez-vous
                        </a>
                        <a href="{{ route('services') }}" class="btn btn-outline-light btn-lg rounded-pill">
                            Découvrir nos services
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Points forts --}}
    <section class="section pb-0">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-hover h-100 border-0 shadow-sm">
                        <div class="card-body d-flex gap-3">
                            <span class="icon-circle"><i class="bi bi-calendar2-check"></i></span>
                            <div>
                                <h2 class="h5">Rendez-vous en ligne</h2>
                                <p class="small mb-0 text-muted">
                                    Choisissez votre spécialité, votre médecin et votre créneau en quelques clics,
                                    24h/24, sans créer de compte.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-hover h-100 border-0 shadow-sm">
                        <div class="card-body d-flex gap-3">
                            <span class="icon-circle"><i class="bi bi-heart-pulse"></i></span>
                            <div>
                                <h2 class="h5">Urgences 24h/24</h2>
                                <p class="small mb-0 text-muted">
                                    Une équipe disponible jour et nuit, 7 jours sur 7 :
                                    <a href="tel:{{ preg_replace('/\s+/', '', setting('emergency_phone', '')) }}" class="fw-bold">{{ setting('emergency_phone') }}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-hover h-100 border-0 shadow-sm">
                        <div class="card-body d-flex gap-3">
                            <span class="icon-circle"><i class="bi bi-whatsapp"></i></span>
                            <div>
                                <h2 class="h5">Confirmation WhatsApp</h2>
                                <p class="small mb-0 text-muted">
                                    Recevez automatiquement la confirmation et le rappel de votre
                                    rendez-vous directement sur WhatsApp.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Spécialités --}}
    <section class="section">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-eyebrow">Nos spécialités</span>
                <h2>Une équipe pluridisciplinaire à votre écoute</h2>
            </div>
            <div class="row g-4">
                @foreach ($specialties as $specialty)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card card-hover h-100 text-center border-0 shadow-sm">
                            <div class="card-body">
                                <span class="icon-circle mb-3"><i class="bi {{ $specialty->icon ?? 'bi-plus-circle' }}"></i></span>
                                <h3 class="h6 mb-2">{{ $specialty->name }}</h3>
                                <p class="small text-muted d-none d-md-block mb-0">{{ Str::limit($specialty->description, 70) }}</p>
                            </div>
                            <div class="card-footer bg-transparent border-0 pb-3">
                                <a href="{{ route('appointments.create', ['specialite' => $specialty->slug]) }}" class="small fw-semibold">
                                    Prendre rendez-vous <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('services') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Voir tous nos services
                </a>
            </div>
        </div>
    </section>

    {{-- Comment ça marche --}}
    <section class="section bg-cream">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-eyebrow">Simple et rapide</span>
                <h2>Votre rendez-vous en moins d'une minute</h2>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <div class="icon-circle mx-auto mb-3"><i class="bi bi-1-circle"></i></div>
                    <h3 class="h6">Choisissez la spécialité</h3>
                    <p class="small text-muted">Et le médecin de votre choix, ou « le premier disponible ».</p>
                </div>
                <div class="col-md-3">
                    <div class="icon-circle mx-auto mb-3"><i class="bi bi-2-circle"></i></div>
                    <h3 class="h6">Sélectionnez le créneau</h3>
                    <p class="small text-muted">Les disponibilités réelles s'affichent en temps réel.</p>
                </div>
                <div class="col-md-3">
                    <div class="icon-circle mx-auto mb-3"><i class="bi bi-3-circle"></i></div>
                    <h3 class="h6">Indiquez vos coordonnées</h3>
                    <p class="small text-muted">Nom, prénom, téléphone : rien de plus, sans compte.</p>
                </div>
                <div class="col-md-3">
                    <div class="icon-circle mx-auto mb-3"><i class="bi bi-whatsapp"></i></div>
                    <h3 class="h6">Recevez la confirmation</h3>
                    <p class="small text-muted">Accusé immédiat sur WhatsApp, avec votre code de suivi.</p>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('appointments.create') }}" class="btn-rdv">
                    <i class="bi bi-calendar2-heart me-2"></i>Prendre rendez-vous maintenant
                </a>
            </div>
        </div>
    </section>

    {{-- Médecins --}}
    <section class="section">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-eyebrow">Notre équipe</span>
                <h2>Des médecins expérimentés et disponibles</h2>
            </div>
            <div class="row g-4">
                @foreach ($doctors as $doctor)
                    <div class="col-6 col-lg-3">
                        <div class="card card-hover h-100 border-0 shadow-sm overflow-hidden">
                            @if ($doctor->photo)
                                <img src="{{ asset('storage/' . $doctor->photo) }}" alt="{{ $doctor->full_name }}" class="doctor-photo">
                            @else
                                <div class="doctor-photo-placeholder">
                                    <i class="bi bi-person"></i>
                                </div>
                            @endif
                            <div class="card-body text-center">
                                <h3 class="h6 mb-1">{{ $doctor->full_name }}</h3>
                                <p class="small text-primary mb-2">{{ $doctor->specialty->name }}</p>
                                <a href="{{ route('appointments.create', ['medecin' => $doctor->slug]) }}" class="small fw-semibold">
                                    Prendre rendez-vous <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('team') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Toute l'équipe médicale
                </a>
            </div>
        </div>
    </section>

    {{-- Conseil santé + articles (incitation douce) --}}
    <section class="section bg-blue-soft">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5">
                    <span class="section-eyebrow">Prévention</span>
                    <h2 class="mb-4">Le saviez-vous ?</h2>
                    @if ($healthTip)
                        <div class="health-tip mb-4">
                            <strong><i class="bi {{ $healthTip->icon }} me-2"></i>{{ $healthTip->name }}</strong>
                            <p class="mb-2 mt-1">{{ $healthTip->health_tip }}</p>
                            <a href="{{ route('appointments.create', ['specialite' => $healthTip->slug]) }}" class="small fw-semibold">
                                Consulter un spécialiste <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    @endif
                    <p class="text-muted small">
                        Un doute, une question de santé ? Une consultation de prévention prend
                        quelques minutes et peut faire toute la différence.
                    </p>
                </div>
                <div class="col-lg-7">
                    <span class="section-eyebrow">Actualités &amp; conseils</span>
                    <h2 class="mb-4">Nos derniers articles</h2>
                    <div class="d-grid gap-3">
                        @foreach ($articles as $article)
                            <a href="{{ route('articles.show', $article) }}" class="card card-hover border-0 shadow-sm text-decoration-none">
                                <div class="card-body d-flex gap-3 align-items-center">
                                    <span class="icon-circle"><i class="bi bi-journal-medical"></i></span>
                                    <div>
                                        <span class="badge text-bg-light text-primary mb-1">{{ $article->category->label() }}</span>
                                        <h3 class="h6 mb-1 text-dark">{{ $article->title }}</h3>
                                        <p class="small text-muted mb-0">{{ Str::limit($article->excerpt, 90) }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Appel à l'action final, rassurant --}}
    <section class="section text-center">
        <div class="container">
            <h2 class="mb-3">Prenez soin de vous, nous nous occupons du reste.</h2>
            <p class="text-muted mb-4">
                Réservez votre consultation en ligne — c'est simple, rapide et sans engagement.
            </p>
            <a href="{{ route('appointments.create') }}" class="btn-rdv btn-lg">
                <i class="bi bi-calendar2-heart me-2"></i>Prendre rendez-vous
            </a>
        </div>
    </section>

@endsection
