<?php

namespace App\Services;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Jobs\SendAppointmentNotification;
use App\Models\Appointment;
use App\Models\NotificationLog;

/**
 * Prépare, journalise et met en file les notifications patient
 * à chaque étape du rendez-vous (CDC §5.1).
 *
 * Chaque message est d'abord enregistré dans notification_logs avec le statut
 * « en file », puis traité par SendAppointmentNotification qui gère l'envoi
 * WhatsApp et le repli SMS/e-mail.
 */
class AppointmentNotifier
{
    public function booked(Appointment $appointment): void
    {
        $this->dispatch(
            $appointment,
            'rdv_recu',
            $this->buildBookedMessage($appointment),
            [
                $appointment->patient->first_name,
                $appointment->specialty->name,
                $appointment->doctor->full_name,
                ucfirst($appointment->date->translatedFormat('l j F Y')),
                substr($appointment->start_time, 0, 5),
                $appointment->tracking_code,
            ],
        );
    }

    public function confirmed(Appointment $appointment): void
    {
        $this->dispatch(
            $appointment,
            'rdv_confirme',
            $this->buildConfirmedMessage($appointment),
            [
                $appointment->patient->first_name,
                $appointment->specialty->name,
                $appointment->doctor->full_name,
                ucfirst($appointment->date->translatedFormat('l j F Y')),
                substr($appointment->start_time, 0, 5),
                setting('clinic_address', 'Clinique Tamarix, Abidjan'),
            ],
        );
    }

    public function cancelled(Appointment $appointment): void
    {
        $this->dispatch(
            $appointment,
            'rdv_annule',
            $this->buildCancelledMessage($appointment),
            [
                $appointment->patient->first_name,
                $appointment->date->translatedFormat('d/m/Y'),
                substr($appointment->start_time, 0, 5),
            ],
        );
    }

    public function reminder(Appointment $appointment): void
    {
        $this->dispatch(
            $appointment,
            'rappel_j1',
            $this->buildReminderMessage($appointment),
            [
                $appointment->patient->first_name,
                ucfirst($appointment->date->translatedFormat('l j F')),
                substr($appointment->start_time, 0, 5),
                $appointment->doctor->full_name,
            ],
        );
    }

    public function rescheduled(Appointment $appointment): void
    {
        $this->dispatch(
            $appointment,
            'rdv_reporte',
            $this->buildRescheduledMessage($appointment),
            [
                $appointment->patient->first_name,
                ucfirst($appointment->date->translatedFormat('l j F Y')),
                substr($appointment->start_time, 0, 5),
                $appointment->doctor->full_name,
            ],
        );
    }

    /**
     * Journalise le message puis met l'envoi en file d'attente.
     *
     * @param  array<int, string>  $parameters  Variables du modèle Meta
     */
    private function dispatch(Appointment $appointment, string $template, string $content, array $parameters): void
    {
        // Le consentement du patient est requis (CDC §5.2)
        if (! $appointment->patient->whatsapp_consent) {
            return;
        }

        $log = NotificationLog::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'channel' => NotificationChannel::Whatsapp,
            'template' => $template,
            'recipient' => $appointment->patient->phone,
            'content' => $content,
            'status' => NotificationStatus::Queued,
        ]);

        SendAppointmentNotification::dispatch($log->id, $parameters);
    }

    private function buildBookedMessage(Appointment $appointment): string
    {
        return sprintf(
            "Bonjour %s, votre demande de rendez-vous à la Clinique Tamarix a bien été reçue.\n"
            . "🩺 %s — %s\n📅 %s à %s\n"
            . "Statut : en attente de confirmation par notre accueil.\n"
            . "Votre code de suivi : %s\n"
            . "Suivre ou annuler : %s",
            $appointment->patient->first_name,
            $appointment->specialty->name,
            $appointment->doctor->full_name,
            ucfirst($appointment->date->translatedFormat('l j F Y')),
            substr($appointment->start_time, 0, 5),
            $appointment->tracking_code,
            route('appointments.track'),
        );
    }

    private function buildConfirmedMessage(Appointment $appointment): string
    {
        return sprintf(
            "✅ %s, votre rendez-vous à la Clinique Tamarix est confirmé.\n"
            . "🩺 %s — %s\n📅 %s à %s\n📍 %s\n"
            . "Merci d'arriver 15 minutes en avance avec une pièce d'identité.\n"
            . "Code de suivi : %s — Suivre ou annuler : %s",
            $appointment->patient->first_name,
            $appointment->specialty->name,
            $appointment->doctor->full_name,
            ucfirst($appointment->date->translatedFormat('l j F Y')),
            substr($appointment->start_time, 0, 5),
            setting('clinic_address', 'Clinique Tamarix, Abidjan'),
            $appointment->tracking_code,
            route('appointments.track'),
        );
    }

    private function buildCancelledMessage(Appointment $appointment): string
    {
        return sprintf(
            "%s, votre rendez-vous du %s à %s à la Clinique Tamarix a bien été annulé.\n"
            . "Pour reprendre rendez-vous : %s\nÀ très bientôt.",
            $appointment->patient->first_name,
            $appointment->date->translatedFormat('d/m/Y'),
            substr($appointment->start_time, 0, 5),
            route('appointments.create'),
        );
    }

    private function buildRescheduledMessage(Appointment $appointment): string
    {
        return sprintf(
            "📅 %s, votre rendez-vous à la Clinique Tamarix a été reprogrammé.\n"
            . "Nouveau créneau : %s à %s\n🩺 %s — %s\n"
            . "Code de suivi inchangé : %s — Suivre ou annuler : %s",
            $appointment->patient->first_name,
            ucfirst($appointment->date->translatedFormat('l j F Y')),
            substr($appointment->start_time, 0, 5),
            $appointment->specialty->name,
            $appointment->doctor->full_name,
            $appointment->tracking_code,
            route('appointments.track'),
        );
    }

    private function buildReminderMessage(Appointment $appointment): string
    {
        return sprintf(
            "🔔 Rappel : %s, vous avez rendez-vous demain %s à %s à la Clinique Tamarix (%s, %s).\n"
            . "En cas d'empêchement, merci d'annuler : %s",
            $appointment->patient->first_name,
            ucfirst($appointment->date->translatedFormat('l j F')),
            substr($appointment->start_time, 0, 5),
            $appointment->specialty->name,
            $appointment->doctor->full_name,
            route('appointments.track'),
        );
    }
}
