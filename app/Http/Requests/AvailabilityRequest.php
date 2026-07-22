<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'weekday' => ['required', 'integer', 'between:1,7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_duration' => ['required', 'integer', 'in:15,20,30,45,60'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'weekday' => 'jour',
            'start_time' => 'heure de début',
            'end_time' => 'heure de fin',
            'slot_duration' => 'durée du créneau',
        ];
    }
}
