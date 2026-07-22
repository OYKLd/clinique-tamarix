@extends('layouts.public')

@section('title', 'La Clinique — Clinique Médico-Chirurgicale Tamarix')
@section('meta_description', 'Découvrez la Clinique Médico-Chirurgicale Tamarix à Abidjan : notre histoire, nos valeurs, notre engagement qualité et notre plateau technique moderne.')

@section('content')
    <x-page-header title="La Clinique" subtitle="Un établissement moderne, rigoureux et humain" />

    {{-- Qui sommes-nous --}}
    <section class="section">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <span class="section-eyebrow">Qui sommes-nous ?</span>
                    <h2 class="mb-4">Une clinique née d'une conviction : chaque patient mérite l'excellence</h2>
                    <p>
                        La Clinique Médico-Chirurgicale Tamarix est une structure de santé privée installée à Abidjan.
                        Elle réunit une équipe médicale pluridisciplinaire expérimentée autour d'un plateau technique
                        moderne : imagerie, laboratoire, bloc opératoire et service d'urgences ouvert 24h/24.
                    </p>
                    <p>
                        Comme le tamarix — cet arbre qui prospère là où peu d'autres survivent — nous croyons que
                        l'espérance se plante, se cultive et porte ses fruits. C'est le sens de notre devise :
                        <em>« Nous plantons l'Espérance »</em>.
                    </p>
                </div>
                <div class="col-lg-6">
                    <img src="{{ asset('images/hero.webp') }}" alt="Façade de la Clinique Tamarix"
                         class="img-fluid rounded-4 shadow">
                </div>
            </div>
        </div>
    </section>

    {{-- Mot de la direction --}}
    <section class="section bg-cream">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 text-center">
                    <span class="section-eyebrow">Le mot de la direction</span>
                    <blockquote class="fs-5 fst-italic mt-3 mb-4">
                        « Notre ambition est simple : offrir aux familles d'Abidjan une médecine de standard
                        international, dans un cadre où chacun est accueilli avec écoute, respect et bienveillance.
                        Chaque membre de notre équipe s'engage, chaque jour, à mériter votre confiance. »
                    </blockquote>
                    <p class="fw-semibold text-secondary mb-0">La Direction — Clinique Médico-Chirurgicale Tamarix</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Valeurs --}}
    <section class="section">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-eyebrow">Nos valeurs</span>
                <h2>Ce qui guide chacun de nos gestes</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card card-hover h-100 border-0 shadow-sm text-center">
                        <div class="card-body">
                            <span class="icon-circle mb-3"><i class="bi bi-flower1"></i></span>
                            <h3 class="h6">Espérance</h3>
                            <p class="small text-muted mb-0">Redonner confiance et perspective à chaque patient, quelle que soit sa situation.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card card-hover h-100 border-0 shadow-sm text-center">
                        <div class="card-body">
                            <span class="icon-circle mb-3"><i class="bi bi-award"></i></span>
                            <h3 class="h6">Excellence</h3>
                            <p class="small text-muted mb-0">Des soins conformes aux standards internationaux, portés par la formation continue.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card card-hover h-100 border-0 shadow-sm text-center">
                        <div class="card-body">
                            <span class="icon-circle mb-3"><i class="bi bi-people"></i></span>
                            <h3 class="h6">Humanité</h3>
                            <p class="small text-muted mb-0">L'écoute, le respect et la dignité du patient au centre de la prise en charge.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card card-hover h-100 border-0 shadow-sm text-center">
                        <div class="card-body">
                            <span class="icon-circle mb-3"><i class="bi bi-shield-check"></i></span>
                            <h3 class="h6">Intégrité</h3>
                            <p class="small text-muted mb-0">Transparence des actes, des tarifs et des décisions médicales.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Plateau technique / engagement qualité --}}
    <section class="section bg-blue-soft">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <span class="section-eyebrow">Plateau technique</span>
                    <h2 class="mb-4">Des équipements de dernière génération</h2>
                    <ul class="list-unstyled d-grid gap-3">
                        <li class="d-flex gap-3">
                            <span class="icon-circle"><i class="bi bi-camera2"></i></span>
                            <div>
                                <strong>Imagerie médicale</strong>
                                <p class="small text-muted mb-0">Radiographie et échographie pour des diagnostics rapides et précis.</p>
                            </div>
                        </li>
                        <li class="d-flex gap-3">
                            <span class="icon-circle"><i class="bi bi-droplet"></i></span>
                            <div>
                                <strong>Laboratoire d'analyses</strong>
                                <p class="small text-muted mb-0">Bilans complets réalisés sur place, résultats dans les meilleurs délais.</p>
                            </div>
                        </li>
                        <li class="d-flex gap-3">
                            <span class="icon-circle"><i class="bi bi-scissors"></i></span>
                            <div>
                                <strong>Bloc opératoire</strong>
                                <p class="small text-muted mb-0">Chirurgie programmée et d'urgence dans des conditions de sécurité optimales.</p>
                            </div>
                        </li>
                        <li class="d-flex gap-3">
                            <span class="icon-circle"><i class="bi bi-heart-pulse"></i></span>
                            <div>
                                <strong>Urgences 24h/24</strong>
                                <p class="small text-muted mb-0">Une équipe mobilisée jour et nuit, 7 jours sur 7 : {{ setting('emergency_phone') }}.</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <span class="section-eyebrow">Engagement qualité</span>
                    <h2 class="mb-4">Notre promesse envers vous</h2>
                    <div class="d-grid gap-3">
                        <div class="card border-0 shadow-sm"><div class="card-body d-flex gap-3">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            <span>Hygiène et stérilisation conformes aux protocoles internationaux.</span>
                        </div></div>
                        <div class="card border-0 shadow-sm"><div class="card-body d-flex gap-3">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            <span>Confidentialité absolue des dossiers médicaux.</span>
                        </div></div>
                        <div class="card border-0 shadow-sm"><div class="card-body d-flex gap-3">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            <span>Délais d'attente réduits grâce à la prise de rendez-vous en ligne.</span>
                        </div></div>
                        <div class="card border-0 shadow-sm"><div class="card-body d-flex gap-3">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            <span>Information claire du patient avant tout acte médical.</span>
                        </div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="section text-center">
        <div class="container">
            <h2 class="mb-3">Venez nous rencontrer</h2>
            <p class="text-muted mb-4">Notre équipe vous accueille du lundi au samedi — et 24h/24 pour les urgences.</p>
            <div class="d-flex justify-content-center flex-wrap gap-3">
                <a href="{{ route('appointments.create') }}" class="btn-rdv"><i class="bi bi-calendar2-heart me-2"></i>Prendre rendez-vous</a>
                <a href="{{ route('contact') }}" class="btn btn-outline-secondary rounded-pill px-4">Nous contacter</a>
            </div>
        </div>
    </section>
@endsection
