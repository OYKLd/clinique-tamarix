<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $doctorId = $this->route('doctor')?->id;

        return [
            'specialty_id' => ['required', 'exists:specialties,id'],
            'title' => ['required', 'string', 'max:20'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'user_id' => ['nullable', 'exists:users,id', Rule::unique('doctors', 'user_id')->ignore($doctorId)],
        ];
    }

    public function attributes(): array
    {
        return [
            'specialty_id' => 'spécialité',
            'title' => 'titre',
            'first_name' => 'prénom',
            'last_name' => 'nom',
            'bio' => 'biographie',
            'phone' => 'téléphone',
            'photo' => 'photo',
            'user_id' => 'compte back-office',
            'sort_order' => 'ordre d\'affichage',
        ];
    }
}
