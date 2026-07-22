@extends('layouts.public')

@section('title', 'Demande de rendez-vous enregistrée — Clinique Tamarix')

@section('content')
    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body p-4 p-md-5">
                            <div class="icon-circle mx-auto mb-3 bg-success-subtle text-success" style="width:4.5rem;height:4.5rem;font-size:2rem;">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <h1 class="h3 mb-2">Votre demande est enregistrée !</h1>
                            <p class="text-muted mb-4">
                                Notre accueil va la confirmer dans les meilleurs délais.
                                Vous recevrez la confirmation sur WhatsApp.
                            </p>

                            <div class="bg-cream rounded-4 p-4 mb-4">
                                <p class="small text-muted mb-1">Votre code de suivi</p>
                                <p class="display-6 fw-bold text-secondary mb-0" style="letter-spacing:0.08em;">
                                    {{ $appointment->tracking_code }}
                                </p>
                                <p class="small text-muted mt-2 mb-0">
                                    Conservez-le précieusement : il vous permet de suivre ou d'annuler
                                    votre rendez-vous à tout moment, sans compte.
                                </p>
                            </div>

                            <div class="text-start bg-blue-soft rounded-4 p-4 mb-4">
                                <p class="fw-semibold mb-3"><i class="bi bi-card-checklist me-2"></i>Récapitulatif</p>
                                <ul class="list-unstyled d-grid gap-2 small mb-0">
                                    <li><i class="bi bi-clipboard2-pulse me-2 text-primary"></i><strong>Spécialité :</strong> {{ $appointment->specialty->name }}</li>
                                    <li><i class="bi bi-person-badge me-2 text-primary"></i><strong>Médecin :</strong> {{ $appointment->doctor->full_name }}</li>
                                    <li><i class="bi bi-calendar3 me-2 text-primary"></i><strong>Date :</strong> {{ ucfirst($appointment->date->translatedFormat('l j F Y')) }}</li>
                                    <li><i class="bi bi-clock me-2 text-primary"></i><strong>Heure :</strong> {{ substr($appointment->start_time, 0, 5) }}</li>
                                    <li>
                                        <i class="bi bi-hourglass-split me-2 text-primary"></i><strong>Statut :</strong>
                                        <span class="badge {{ $appointment->status->badgeClass() }}">{{ $appointment->status->label() }}</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="d-flex flex-wrap justify-content-center gap-3">
                                <a href="{{ route('appointments.track') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="bi bi-search me-1"></i>Suivre mon rendez-vous
                                </a>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                    Retour à l'accueil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
