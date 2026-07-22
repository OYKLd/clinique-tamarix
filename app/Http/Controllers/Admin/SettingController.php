<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Paramètres modifiables depuis le back-office, groupés par section.
     */
    private const FIELDS = [
        'Identité' => [
            'clinic_name' => ['label' => 'Nom de la clinique', 'rules' => 'required|string|max:150'],
            'clinic_slogan' => ['label' => 'Slogan', 'rules' => 'nullable|string|max:150'],
        ],
        'Coordonnées' => [
            'clinic_phone' => ['label' => 'Téléphone standard', 'rules' => 'required|string|max:30'],
            'emergency_phone' => ['label' => 'Téléphone des urgences (24h/24)', 'rules' => 'required|string|max:30'],
            'whatsapp_number' => ['label' => 'Numéro WhatsApp Business', 'rules' => 'required|string|max:30'],
            'clinic_email' => ['label' => 'Adresse e-mail', 'rules' => 'required|email|max:150'],
            'clinic_address' => ['label' => 'Adresse postale', 'rules' => 'required|string|max:255'],
            'clinic_hours' => ['label' => 'Horaires d\'ouverture', 'rules' => 'required|string|max:255'],
        ],
        'Localisation & réseaux' => [
            'maps_embed_url' => ['label' => 'URL d\'intégration Google Maps', 'rules' => 'nullable|url|max:500'],
            'facebook_url' => ['label' => 'Page Facebook', 'rules' => 'nullable|url|max:255'],
            'instagram_url' => ['label' => 'Compte Instagram', 'rules' => 'nullable|url|max:255'],
            'linkedin_url' => ['label' => 'Page LinkedIn', 'rules' => 'nullable|url|max:255'],
        ],
    ];

    public function edit(): View
    {
        return view('admin.settings.edit', [
            'sections' => self::FIELDS,
            'values' => Setting::pluck('value', 'key')->all(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [];
        $attributes = [];

        foreach (self::FIELDS as $fields) {
            foreach ($fields as $key => $field) {
                $rules[$key] = $field['rules'];
                $attributes[$key] = mb_strtolower($field['label']);
            }
        }

        $validated = $request->validate($rules, [], $attributes);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        ActivityLog::record('settings.updated', null, 'Paramètres de la clinique mis à jour');

        return back()->with('success', 'Paramètres enregistrés. Le site public est mis à jour immédiatement.');
    }
}
