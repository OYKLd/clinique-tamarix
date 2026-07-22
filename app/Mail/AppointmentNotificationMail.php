<?php

namespace App\Mail;

use App\Models\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly NotificationLog $log,
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'rdv_recu' => 'Votre demande de rendez-vous a bien été reçue',
            'rdv_confirme' => 'Votre rendez-vous est confirmé',
            'rappel_j1' => 'Rappel : votre rendez-vous demain',
            'rdv_annule' => 'Votre rendez-vous a été annulé',
            'rdv_reporte' => 'Votre rendez-vous a été reprogrammé',
        ];

        return new Envelope(
            subject: ($subjects[$this->log->template] ?? 'Votre rendez-vous') . ' — Clinique Tamarix',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.appointment-notification');
    }
}
