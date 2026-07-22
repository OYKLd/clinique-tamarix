<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pilote d'envoi
    |--------------------------------------------------------------------------
    | « cloud » : API WhatsApp Business Cloud de Meta (production).
    | « log »   : aucun envoi réel, les messages sont journalisés — utile en
    |             développement et tant que les modèles Meta ne sont pas approuvés.
    */
    'driver' => env('WHATSAPP_DRIVER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | API WhatsApp Business Cloud
    |--------------------------------------------------------------------------
    | Identifiants disponibles dans Meta for Developers → WhatsApp → Configuration.
    */
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
    'api_version' => env('WHATSAPP_API_VERSION', 'v21.0'),
    'base_url' => env('WHATSAPP_BASE_URL', 'https://graph.facebook.com'),

    /*
    | Jeton de vérification du webhook (choisi librement, à saisir à l'identique
    | dans la console Meta lors de la configuration du webhook).
    */
    'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Modèles de messages approuvés par Meta
    |--------------------------------------------------------------------------
    | Chaque modèle doit être créé et approuvé dans le gestionnaire WhatsApp
    | avant utilisation en production. La clé correspond au nom interne utilisé
    | par AppointmentNotifier ; « name » au nom exact du modèle chez Meta.
    |
    | Variables attendues par modèle (dans l'ordre des {{1}}, {{2}}… du modèle) :
    |   rdv_recu      : prénom, spécialité, médecin, date, heure, code de suivi
    |   rdv_confirme  : prénom, spécialité, médecin, date, heure, adresse
    |   rappel_j1     : prénom, date, heure, médecin
    |   rdv_annule    : prénom, date, heure
    |   rdv_reporte   : prénom, nouvelle date, nouvelle heure, médecin
    */
    'templates' => [
        'rdv_recu' => ['name' => env('WHATSAPP_TPL_BOOKED', 'tamarix_rdv_recu'), 'language' => 'fr'],
        'rdv_confirme' => ['name' => env('WHATSAPP_TPL_CONFIRMED', 'tamarix_rdv_confirme'), 'language' => 'fr'],
        'rappel_j1' => ['name' => env('WHATSAPP_TPL_REMINDER', 'tamarix_rappel_j1'), 'language' => 'fr'],
        'rdv_annule' => ['name' => env('WHATSAPP_TPL_CANCELLED', 'tamarix_rdv_annule'), 'language' => 'fr'],
        'rdv_reporte' => ['name' => env('WHATSAPP_TPL_RESCHEDULED', 'tamarix_rdv_reporte'), 'language' => 'fr'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Repli en cas d'échec (CDC §5.2)
    |--------------------------------------------------------------------------
    | Si l'envoi WhatsApp échoue (numéro invalide, non inscrit à WhatsApp…),
    | le message est réémis sur le canal de repli configuré.
    */
    'fallback' => [
        'enabled' => env('WHATSAPP_FALLBACK_ENABLED', true),
        'channel' => env('WHATSAPP_FALLBACK_CHANNEL', 'mail'), // sms | mail
    ],

];
