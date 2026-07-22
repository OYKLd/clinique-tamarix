<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:120'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
            // Champ pot de miel anti-spam : doit rester vide
            'website' => ['prohibited'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'phone' => 'téléphone',
            'email' => 'adresse e-mail',
            'subject' => 'objet',
            'message' => 'message',
        ];
    }
}
