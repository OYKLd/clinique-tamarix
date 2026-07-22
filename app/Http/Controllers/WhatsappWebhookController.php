<?php

namespace App\Http\Controllers;

use App\Enums\NotificationStatus;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Webhook Meta : accusés de réception des messages (délivré / lu / échec).
 * Alimente le journal des envois consultable depuis le tableau de bord (CDC §5.2).
 */
class WhatsappWebhookController extends Controller
{
    /**
     * Vérification initiale du webhook par Meta.
     */
    public function verify(Request $request): Response
    {
        $token = config('whatsapp.webhook_verify_token');

        if ($request->query('hub_mode') === 'subscribe'
            && filled($token)
            && hash_equals($token, (string) $request->query('hub_verify_token'))) {
            return response((string) $request->query('hub_challenge'), 200);
        }

        return response('Vérification refusée', 403);
    }

    /**
     * Réception des changements de statut.
     */
    public function handle(Request $request): Response
    {
        $statuses = data_get($request->all(), 'entry.*.changes.*.value.statuses.*') ?? [];

        foreach ($statuses as $status) {
            $messageId = data_get($status, 'id');

            if (! $messageId) {
                continue;
            }

            $log = NotificationLog::where('provider_message_id', $messageId)->first();

            if (! $log) {
                continue;
            }

            match (data_get($status, 'status')) {
                'delivered' => $log->update([
                    'status' => NotificationStatus::Delivered,
                    'delivered_at' => now(),
                ]),
                'read' => $log->update([
                    'status' => NotificationStatus::Read,
                    'read_at' => now(),
                    'delivered_at' => $log->delivered_at ?? now(),
                ]),
                'failed' => $log->update([
                    'status' => NotificationStatus::Failed,
                    'error' => data_get($status, 'errors.0.title', 'Échec signalé par Meta'),
                ]),
                default => null,
            };
        }

        // Meta attend systématiquement une réponse 200
        return response('', 200);
    }
}
