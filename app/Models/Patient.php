<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'whatsapp_consent',
        'internal_notes',
    ];

    protected function casts(): array
    {
        return [
            'whatsapp_consent' => 'boolean',
        ];
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(
            fn () => trim("{$this->first_name} " . mb_strtoupper($this->last_name))
        );
    }

    /**
     * Normalise un numéro ivoirien en format international +225XXXXXXXXXX.
     * Depuis 2021, les numéros ivoiriens comptent 10 chiffres et conservent
     * leur 0 initial en format international (+225 07 XX XX XX XX).
     */
    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9+]/', '', $phone);

        if (str_starts_with($digits, '00')) {
            $digits = '+' . substr($digits, 2);
        }

        if (! str_starts_with($digits, '+')) {
            $digits = str_starts_with($digits, '225') ? '+' . $digits : '+225' . $digits;
        }

        return $digits;
    }
}
