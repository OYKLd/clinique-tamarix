@extends('layouts.public')

@section('title', 'Suivre ou annuler mon rendez-vous — Clinique Tamarix')
@section('meta_description', 'Retrouvez votre rendez-vous à la Clinique Tamarix avec votre numéro de téléphone et votre code de suivi. Consultez son statut ou annulez-le en un clic, sans compte.')

@section('content')
    <x-page-header title="Suivre / Annuler mon rendez-vous"
                   subtitle="Votre numéro de téléphone et votre code de suivi suffisent — aucun compte nécessaire" />

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">

                    @if (! $appointment)
                        {{-- Formulaire de recherche --}}
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4 p-md-5">
                                <p class="text-muted small mb-4">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Votre code de suivi (ex. <strong>TMX-2607-0451</strong>) vous a été
                                    transmis dans le message WhatsApp reçu après votre réservation.
                                </p>
                                <form method="POST" action="{{ route('appointments.track.search') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="phone" class="form-label fw-semibold">Numéro de téléphone</label>
                                        <input type="tel" class="form-control form-control-lg @error('phone') is-invalid @enderror"
                                               id="phone" name="phone" value="{{ old('phone') }}"
                                               placeholder="+225 07 00 00 00 00" required maxlength="30" autocomplete="tel">
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="tracking_code" class="form-label fw-semibold">Code de suivi</label>
                                        <input type="text" class="form-control form-control-lg text-uppercase @error('tracking_code') is-invalid @enderror"
                                               id="tracking_code" name="tracking_code" value="{{ old('tracking_code') }}"
                                               placeholder="TMX-0000-0000" required maxlength="20"
                                               style="letter-spacing:0.06em;">
                                        @error('tracking_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <button type="submit" class="btn-rdv btn-lg w-100">
                                        <i class="bi bi-search me-2"></i>Retrouver mon rendez-vous
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p class="text-center text-muted small mt-4">
                            Vous n'avez pas encore de rendez-vous ?
                            <a href="{{ route('appointments.create') }}">Réservez en moins d'une minute</a>.
                        </p>

                    @else
                        {{-- Détail du rendez-vous retrouvé --}}
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4 p-md-5">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
                                    <div>
                                        <p class="small text-muted mb-1">Rendez-vous {{ $appointment->tracking_code }}</p>
                                        <h2 class="h4 mb-0">{{ $appointment->patient->full_name }}</h2>
                                    </div>
                                    <span class="badge fs-6 {{ $appointment->status->badgeClass() }}">
                                        {{ $appointment->status->label() }}
                                    </span>
                                </div>

                                <ul class="list-unstyled d-grid gap-3 mb-4">
                                    <li class="d-flex gap-3">
                                        <span class="icon-circle"><i class="bi bi-clipboard2-pulse"></i></span>
                                        <div><strong>{{ $appointment->specialty->name }}</strong><br>
                                            <span class="text-muted small">{{ $appointment->doctor->full_name }}</span></div>
                                    </li>
                                    <li class="d-flex gap-3">
                                        <span class="icon-circle"><i class="bi bi-calendar3"></i></span>
                                        <div><strong>{{ ucfirst($appointment->date->translatedFormat('l j F Y')) }}</strong><br>
                                            <span class="text-muted small">à {{ substr($appointment->start_time, 0, 5) }}</span></div>
                                    </li>
                                    <li class="d-flex gap-3">
                                        <span class="icon-circle"><i class="bi bi-geo-alt"></i></span>
                                        <div><strong>Clinique Tamarix</strong><br>
                                            <span class="text-muted small">{{ setting('clinic_address') }}</span></div>
                                    </li>
                                </ul>

                                @if ($appointment->isPending())
                                    <div class="alert alert-warning small">
                                        <i class="bi bi-hourglass-split me-1"></i>
                                        Votre demande est en cours de confirmation par notre accueil.
                                        Vous recevrez une notification WhatsApp dès qu'elle sera validée.
                                    </div>
                                @elseif ($appointment->isConfirmed())
                                    <div class="alert alert-success small">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Rendez-vous confirmé. Merci d'arriver 15 minutes en avance,
                                        muni(e) d'une pièce d'identité.
                                    </div>
                                @elseif ($appointment->status === \App\Enums\AppointmentStatus::Cancelled)
                                    <div class="alert alert-secondary small">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Ce rendez-vous a été annulé{{ $appointment->cancelled_at ? ' le ' . $appointment->cancelled_at->format('d/m/Y à H\hi') : '' }}.
                                        <a href="{{ route('appointments.create') }}">Reprendre un rendez-vous</a>.
                                    </div>
                                @endif

                                <div class="d-grid gap-2">
                                    @if ($appointment->canBeCancelled())
                                        <form method="POST" action="{{ route('appointments.track.cancel') }}"
                                              onsubmit="return confirm('Confirmez-vous l\'annulation de ce rendez-vous ? Le créneau sera immédiatement libéré.');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger w-100">
                                                <i class="bi bi-x-circle me-2"></i>Annuler ce rendez-vous
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('appointments.track.reset') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary w-100">
                                            <i class="bi bi-arrow-left me-2"></i>Rechercher un autre rendez-vous
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>
@endsection
