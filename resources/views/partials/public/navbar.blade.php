{{-- Bandeau urgences permanent — visible sur toutes les pages (CDC §3.1.7) --}}
<div class="emergency-strip py-1">
    <div class="container d-flex flex-wrap justify-content-center justify-content-md-between align-items-center gap-2">
        <span>
            <span class="badge-live me-1"></span>
            Urgences 24h/24 – 7j/7 :
            <a href="tel:{{ preg_replace('/\s+/', '', setting('emergency_phone', '')) }}">{{ setting('emergency_phone') }}</a>
        </span>
        <span class="d-none d-md-inline">
            <i class="bi bi-clock me-1"></i>{{ setting('clinic_hours') }}
        </span>
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-tamarix sticky-top py-2">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}"
                 alt="Clinique Médico-Chirurgicale Tamarix — Nous plantons l'Espérance"
                 height="52" width="114">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Ouvrir le menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">La Clinique</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('services*') ? 'active' : '' }}" href="{{ route('services') }}">Nos Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('team') ? 'active' : '' }}" href="{{ route('team') }}">Équipe médicale</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('articles.*') ? 'active' : '' }}" href="{{ route('articles.index') }}">Actualités</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                </li>
            </ul>

            <div class="d-flex flex-column flex-lg-row gap-2 align-items-stretch align-items-lg-center">
                <a href="{{ route('appointments.track') }}"
                   class="btn btn-outline-secondary btn-sm rounded-pill px-3 {{ request()->routeIs('appointments.track') ? 'active' : '' }}">
                    <i class="bi bi-search me-1"></i>Suivre mon RDV
                </a>
                <a href="{{ route('appointments.create') }}" class="btn-rdv">
                    <i class="bi bi-calendar2-heart me-1"></i>Prendre rendez-vous
                </a>
            </div>
        </div>
    </div>
</nav>
