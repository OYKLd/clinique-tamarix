<?php

namespace App\Jobs;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Mail\AppointmentNotificationMail;
use App\Models\NotificationLog;
use App\Services\Whatsapp\WhatsappClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Envoie une notification patient via WhatsApp, avec repli SMS/e-mail
 * en cas d'échec (CDC §5.2).
 */
class SendAppointmentNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [30, 120];

    /**
     * @param  array<int, string>  $parameters  Variables du modèle Meta
     */
    public function __construct(
        public readonly int $notificationLogId,
        public readonly array $parameters = [],
    ) {}

    public function handle(WhatsappClient $client): void
    {
        $log = NotificationLog::with(['patient', 'appointment'])->find($this->notificationLogId);

        if (! $log || $log->status === NotificationStatus::Sent) {
            return;
        }

        $result = $client->sendTemplate(
            $log->recipient,
            $log->template,
            $this->parameters,
            (string) $log->content,
        );

        if ($result['success']) {
            $log->update([
                'status' => NotificationStatus::Sent,
                'provider_message_id' => $result['message_id'],
                'sent_at' => now(),
                'error' => null,
            ]);

            return;
        }

        $log->update([
            'status' => NotificationStatus::Failed,
            'error' => $result['error'],
        ]);

        $this->sendFallback($log);
    }

    /**
     * Repli automatique : réémet le message sur le canal configuré.
     */
    private function sendFallback(NotificationLog $log): void
    {
        if (! config('whatsapp.fallback.enabled')) {
            return;
        }

        $channel = config('whatsapp.fallback.channel');

        // Repli e-mail : seulement si le patient a renseigné une adresse
        if ($channel === 'mail' && filled($log->patient?->email)) {
            try {
                Mail::to($log->patient->email)->send(new AppointmentNotificationMail($log));

                NotificationLog::create([
                    'appointment_id' => $log->appointment_id,
                    'patient_id' => $log->patient_id,
                    'channel' => NotificationChannel::Mail,
                    'template' => $log->template,
                    'recipient' => $log->patient->email,
                    'content' => $log->content,
                    'status' => NotificationStatus::Sent,
                    'sent_at' => now(),
                ]);

                return;
            } catch (\Throwable $exception) {
                Log::channel('whatsapp')->error('Échec du repli e-mail', [
                    'notification_log_id' => $log->id,
                    'erreur' => $exception->getMessage(),
                ]);
            }
        }

        // Repli SMS : à brancher sur l'opérateur retenu par la clinique
        if ($channel === 'sms') {
            Log::channel('whatsapp')->warning('Repli SMS à configurer', [
                'notification_log_id' => $log->id,
                'destinataire' => $log->recipient,
                'message' => $log->content,
            ]);
        }
    }
}
