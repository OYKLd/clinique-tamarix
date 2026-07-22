<?php

namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Client de l'API WhatsApp Business Cloud (Meta).
 *
 * En mode « log » (défaut en développement, et tant que les modèles ne sont pas
 * approuvés par Meta), aucun appel réseau n'est effectué : le message est écrit
 * dans les journaux applicatifs et considéré comme envoyé.
 */
class WhatsappClient
{
    public function isLive(): bool
    {
        return config('whatsapp.driver') === 'cloud'
            && filled(config('whatsapp.access_token'))
            && filled(config('whatsapp.phone_number_id'));
    }

    /**
     * Envoie un message fondé sur un modèle approuvé.
     *
     * @param  array<int, string>  $parameters  Variables {{1}}, {{2}}… du modèle
     * @return array{success: bool, message_id: ?string, error: ?string}
     */
    public function sendTemplate(string $recipient, string $templateKey, array $parameters, string $preview = ''): array
    {
        $template = config("whatsapp.templates.{$templateKey}");

        if (! $template) {
            return $this->failure("Modèle « {$templateKey} » non configuré.");
        }

        if (! $this->isLive()) {
            Log::channel('whatsapp')->info('[SIMULATION] Message WhatsApp', [
                'destinataire' => $recipient,
                'modele' => $template['name'],
                'variables' => $parameters,
                'apercu' => $preview,
            ]);

            return ['success' => true, 'message_id' => 'simulated-' . uniqid(), 'error' => null];
        }

        try {
            $response = Http::withToken(config('whatsapp.access_token'))
                ->timeout(15)
                ->retry(2, 500, throw: false)
                ->post($this->endpoint(), [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $this->normalizeRecipient($recipient),
                    'type' => 'template',
                    'template' => [
                        'name' => $template['name'],
                        'language' => ['code' => $template['language']],
                        'components' => [[
                            'type' => 'body',
                            'parameters' => array_map(
                                fn (string $value) => ['type' => 'text', 'text' => $value],
                                array_values($parameters),
                            ),
                        ]],
                    ],
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message_id' => $response->json('messages.0.id'),
                    'error' => null,
                ];
            }

            return $this->failure(
                $response->json('error.message') ?? "Erreur HTTP {$response->status()}",
            );
        } catch (\Throwable $exception) {
            return $this->failure($exception->getMessage());
        }
    }

    private function endpoint(): string
    {
        return sprintf(
            '%s/%s/%s/messages',
            rtrim(config('whatsapp.base_url'), '/'),
            config('whatsapp.api_version'),
            config('whatsapp.phone_number_id'),
        );
    }

    /**
     * L'API Meta attend un numéro international sans « + » ni séparateurs.
     */
    private function normalizeRecipient(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    private function failure(string $error): array
    {
        Log::channel('whatsapp')->error('Échec d\'envoi WhatsApp', ['erreur' => $error]);

        return ['success' => false, 'message_id' => null, 'error' => $error];
    }
}
