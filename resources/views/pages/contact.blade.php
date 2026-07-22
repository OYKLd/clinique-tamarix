@extends('layouts.public')

@section('title', 'Contact & Localisation — Clinique Tamarix Abidjan')
@section('meta_description', 'Contactez la Clinique Tamarix à Abidjan : formulaire, téléphone, WhatsApp, e-mail, itinéraire et horaires d\'ouverture. Urgences 24h/24.')

@section('content')
    <x-page-header title="Contact" subtitle="Une question ? Notre équipe vous répond rapidement" />

    <section class="section">
        <div class="container">
            <div class="row g-5">

                {{-- Formulaire --}}
                <div class="col-lg-7">
                    <h2 class="h4 mb-4">Écrivez-nous</h2>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>Merci de corriger les champs signalés ci-dessous.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}" novalidate>
                        @csrf

                        {{-- Pot de miel anti-spam, invisible pour les humains --}}
                        <div class="d-none" aria-hidden="true">
                            <label>Ne pas remplir <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required maxlength="120">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}" maxlength="30" placeholder="+225 …">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Adresse e-mail</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" maxlength="120">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="subject" class="form-label">Objet</label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                       id="subject" name="subject" value="{{ old('subject') }}" maxlength="150">
                                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('message') is-invalid @enderror"
                                          id="message" name="message" rows="5" required maxlength="3000">{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-rdv">
                                    <i class="bi bi-send me-2"></i>Envoyer le message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Coordonnées --}}
                <div class="col-lg-5">
                    <h2 class="h4 mb-4">Nos coordonnées</h2>
                    <ul class="list-unstyled d-grid gap-3 mb-4">
                        <li class="d-flex gap-3">
                            <span class="icon-circle"><i class="bi bi-geo-alt"></i></span>
                            <div><strong>Adresse</strong><br><span class="text-muted">{{ setting('clinic_address') }}</span></div>
                        </li>
                        <li class="d-flex gap-3">
                            <span class="icon-circle"><i class="bi bi-telephone"></i></span>
                            <div>
                                <strong>Téléphone</strong><br>
                                <a href="tel:{{ preg_replace('/\s+/', '', setting('clinic_phone', '')) }}">{{ setting('clinic_phone') }}</a>
                            </div>
                        </li>
                        <li class="d-flex gap-3">
                            <span class="icon-circle"><i class="bi bi-whatsapp"></i></span>
                            <div>
                                <strong>WhatsApp</strong><br>
                                <a href="{{ whatsapp_link('Bonjour, je souhaite des informations sur la Clinique Tamarix.') }}" target="_blank" rel="noopener">
                                    {{ setting('whatsapp_number') }}
                                </a>
                            </div>
                        </li>
                        <li class="d-flex gap-3">
                            <span class="icon-circle"><i class="bi bi-envelope"></i></span>
                            <div>
                                <strong>E-mail</strong><br>
                                <a href="mailto:{{ setting('clinic_email') }}">{{ setting('clinic_email') }}</a>
                            </div>
                        </li>
                        <li class="d-flex gap-3">
                            <span class="icon-circle bg-danger-subtle text-danger"><i class="bi bi-heart-pulse"></i></span>
                            <div>
                                <strong>Urgences 24h/24 — 7j/7</strong><br>
                                <a href="tel:{{ preg_replace('/\s+/', '', setting('emergency_phone', '')) }}" class="fw-bold text-danger">
                                    {{ setting('emergency_phone') }}
                                </a>
                            </div>
                        </li>
                        <li class="d-flex gap-3">
                            <span class="icon-circle"><i class="bi bi-clock"></i></span>
                            <div><strong>Horaires</strong><br><span class="text-muted">{{ setting('clinic_hours') }}</span></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Carte --}}
    <section class="pb-5">
        <div class="container">
            <div class="ratio ratio-21x9 rounded-4 overflow-hidden shadow-sm">
                <iframe src="{{ setting('maps_embed_url') }}"
                        style="border:0" allowfullscreen loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Localisation de la Clinique Tamarix"></iframe>
            </div>
        </div>
    </section>
@endsection
