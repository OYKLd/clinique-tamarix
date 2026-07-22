@extends('layouts.public')

@section('title', 'Politique de confidentialité — Clinique Tamarix')

@section('content')
    <x-page-header title="Politique de confidentialité" />

    <section class="section">
        <div class="container" style="max-width: 860px;">
            <p>
                La Clinique Médico-Chirurgicale Tamarix accorde la plus grande importance à la protection
                de vos données personnelles et de santé. La présente politique décrit les données collectées
                via ce site, leur usage et vos droits, conformément à la loi ivoirienne n° 2013-450 du
                19 juin 2013 relative à la protection des données à caractère personnel.
            </p>

            <h2 class="h5 mt-4">1. Données collectées</h2>
            <ul>
                <li><strong>Prise de rendez-vous :</strong> nom, prénom, numéro de téléphone, spécialité et
                    créneau choisis, motif de consultation éventuel.</li>
                <li><strong>Formulaire de contact :</strong> nom, coordonnées et contenu du message.</li>
                <li><strong>Données techniques :</strong> journaux de connexion nécessaires à la sécurité du site.</li>
            </ul>

            <h2 class="h5 mt-4">2. Finalités</h2>
            <ul>
                <li>Gestion des rendez-vous médicaux et de la relation patient.</li>
                <li>Envoi de notifications liées à votre rendez-vous (accusé de réception, confirmation,
                    rappel, annulation) via WhatsApp, SMS ou e-mail, <strong>avec votre consentement</strong>
                    recueilli lors de la réservation.</li>
                <li>Réponse à vos demandes d'information.</li>
                <li>Statistiques internes anonymisées d'amélioration du service.</li>
            </ul>

            <h2 class="h5 mt-4">3. Base légale et conservation</h2>
            <p>
                Les données sont traitées sur la base de votre consentement et de l'intérêt légitime de la
                clinique à organiser les soins. Elles sont conservées pendant la durée nécessaire à la gestion
                de votre dossier, puis archivées conformément aux obligations légales applicables aux
                établissements de santé.
            </p>

            <h2 class="h5 mt-4">4. Destinataires</h2>
            <p>
                Vos données sont accessibles uniquement au personnel habilité de la clinique (accueil,
                personnel médical concerné, administration), chacun selon son niveau d'accès. Elles ne sont
                jamais vendues ni cédées à des tiers à des fins commerciales.
            </p>

            <h2 class="h5 mt-4">5. Sécurité</h2>
            <p>
                Les données sont hébergées sur des serveurs sécurisés, chiffrées en transit (HTTPS),
                protégées par des contrôles d'accès stricts et sauvegardées quotidiennement.
            </p>

            <h2 class="h5 mt-4">6. Vos droits</h2>
            <p>
                Vous disposez d'un droit d'accès, de rectification, d'opposition et de suppression de vos
                données. Pour l'exercer, contactez-nous à
                <a href="mailto:{{ setting('clinic_email') }}">{{ setting('clinic_email') }}</a> ou à l'accueil
                de la clinique. Vous pouvez également saisir l'ARTCI, autorité de protection des données en
                Côte d'Ivoire.
            </p>

            <h2 class="h5 mt-4">7. Annulation des notifications</h2>
            <p>
                Vous pouvez à tout moment demander l'arrêt des notifications WhatsApp en répondant
                « STOP » au message reçu ou en contactant l'accueil.
            </p>
        </div>
    </section>
@endsection
