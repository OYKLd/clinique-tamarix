<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $isUpdate = $userId !== null;

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($userId)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['boolean'],
            // Mot de passe obligatoire à la création, facultatif en modification
            'password' => [$isUpdate ? 'nullable' : 'required', 'confirmed', Password::min(10)->letters()->numbers()],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'email' => 'adresse e-mail',
            'role' => 'rôle',
            'phone' => 'téléphone',
            'password' => 'mot de passe',
        ];
    }
}
