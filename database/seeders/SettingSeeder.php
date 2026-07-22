<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Valeurs par défaut — à remplacer par les vraies coordonnées de la clinique
     * depuis le back-office (Paramètres) avant la mise en production.
     */
    public function run(): void
    {
        $settings = [
            'clinic_name' => 'Clinique Médico-Chirurgicale Tamarix',
            'clinic_slogan' => 'Nous plantons l\'Espérance',
            'clinic_phone' => '+225 27 00 00 00 00',
            'emergency_phone' => '+225 07 00 00 00 00',
            'whatsapp_number' => '+225 07 00 00 00 00',
            'clinic_email' => 'contact@clinique-tamarix.ci',
            'clinic_address' => 'Abidjan, Côte d\'Ivoire',
            'clinic_hours' => 'Lundi – Samedi : 08h00 – 18h00 | Urgences : 24h/24, 7j/7',
            'maps_embed_url' => 'https://www.google.com/maps?q=Abidjan&output=embed',
            'facebook_url' => '',
            'instagram_url' => '',
            'linkedin_url' => '',
        ];

        foreach ($settings as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
