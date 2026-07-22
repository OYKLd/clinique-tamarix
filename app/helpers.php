<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Lit un paramètre de la clinique (téléphone, adresse, horaires…)
     * stocké en base et mis en cache.
     */
    function setting(string $key, ?string $default = null): ?string
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('whatsapp_link')) {
    /**
     * Lien wa.me à partir du numéro WhatsApp de la clinique.
     */
    function whatsapp_link(?string $text = null): string
    {
        $number = preg_replace('/[^0-9]/', '', setting('whatsapp_number', ''));
        $url = "https://wa.me/{$number}";

        return $text ? $url . '?text=' . rawurlencode($text) : $url;
    }
}
