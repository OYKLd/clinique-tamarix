<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'specialite' => ['required', 'exists:specialties,slug'],
            'medecin' => ['required', 'string'], // slug du médecin ou « any » (premier disponible)
            'date' => ['required', 'date', 'after_or_equal:today'],
            'heure' => ['required', 'date_format:H:i'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\s().-]{8,}$/'],
            'reason' => ['nullable', 'string', 'max:255'],
            'whatsapp_consent' => ['accepted'],
            // Pot de miel anti-spam
            'website' => ['prohibited'],
        ];
    }

    public function attributes(): array
    {
        return [
            'specialite' => 'spécialité',
            'medecin' => 'médecin',
            'date' => 'date',
            'heure' => 'créneau horaire',
            'first_name' => 'prénom',
            'last_name' => 'nom',
            'phone' => 'numéro de téléphone',
            'reason' => 'motif',
            'whatsapp_consent' => 'consentement aux notifications',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Le numéro de téléphone saisi n\'est pas valide.',
            'whatsapp_consent.accepted' => 'Merci d\'accepter de recevoir les notifications liées à votre rendez-vous.',
        ];
    }
}
