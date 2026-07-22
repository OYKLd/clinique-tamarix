@extends('layouts.public')

@section('title', 'Mentions légales — Clinique Tamarix')

@section('content')
    <x-page-header title="Mentions légales" />

    <section class="section">
        <div class="container" style="max-width: 860px;">
            <h2 class="h5">Éditeur du site</h2>
            <p>
                Le présent site est édité par la <strong>Clinique Médico-Chirurgicale Tamarix</strong>,
                établissement de santé privé de droit ivoirien.<br>
                Adresse : {{ setting('clinic_address') }}<br>
                Téléphone : {{ setting('clinic_phone') }} — E-mail : {{ setting('clinic_email') }}
            </p>
            <p class="text-muted small">
                <em>À compléter avant la mise en production : forme juridique, capital social, numéro RCCM,
                numéro de compte contribuable, autorisation d'ouverture délivrée par le Ministère de la Santé,
                nom du directeur de la publication.</em>
            </p>

            <h2 class="h5 mt-4">Hébergement</h2>
            <p class="text-muted small">
                <em>À compléter : nom, raison sociale et coordonnées de l'hébergeur retenu.</em>
            </p>

            <h2 class="h5 mt-4">Propriété intellectuelle</h2>
            <p>
                L'ensemble des contenus du site (textes, images, logo, charte graphique) est la propriété
                exclusive de la Clinique Tamarix, sauf mention contraire. Toute reproduction, représentation
                ou diffusion, totale ou partielle, sans autorisation écrite préalable est interdite.
            </p>

            <h2 class="h5 mt-4">Données personnelles</h2>
            <p>
                Les traitements de données à caractère personnel réalisés via ce site sont décrits dans notre
                <a href="{{ route('legal.privacy') }}">politique de confidentialité</a>. Conformément à la loi
                n° 2013-450 du 19 juin 2013 relative à la protection des données à caractère personnel en
                Côte d'Ivoire, ces traitements sont soumis aux exigences de l'ARTCI.
            </p>

            <h2 class="h5 mt-4">Responsabilité</h2>
            <p>
                Les informations diffusées sur ce site le sont à titre informatif et ne remplacent en aucun
                cas une consultation médicale. En cas d'urgence, contactez immédiatement le
                {{ setting('emergency_phone') }} ou rendez-vous au service des urgences.
            </p>
        </div>
    </section>
@endsection
