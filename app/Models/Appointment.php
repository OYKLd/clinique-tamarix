<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Appointment extends Model
{
    protected $fillable = [
        'tracking_code',
        'patient_id',
        'doctor_id',
        'specialty_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'reason',
        'is_new_patient',
        'source',
        'cancelled_by',
        'cancellation_reason',
        'confirmed_at',
        'cancelled_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => AppointmentStatus::class,
            'is_new_patient' => 'boolean',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Appointment $appointment) {
            if (empty($appointment->tracking_code)) {
                $appointment->tracking_code = self::generateTrackingCode($appointment->date);
            }
        });
    }

    /**
     * Génère un code de suivi unique au format TMX-JJMM-XXXX (ex. TMX-2607-0451).
     */
    public static function generateTrackingCode(Carbon|string|null $date = null): string
    {
        $date = $date ? Carbon::parse($date) : now();

        do {
            $code = sprintf('TMX-%s-%04d', $date->format('dm'), random_int(0, 9999));
        } while (self::where('tracking_code', $code)->exists());

        return $code;
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    /**
     * RDV occupant réellement un créneau (en attente ou confirmés).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', AppointmentStatus::activeStatuses());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('date', '>=', today());
    }

    public function scopeForDate(Builder $query, Carbon|string $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    public function isPending(): bool
    {
        return $this->status === AppointmentStatus::Pending;
    }

    public function isConfirmed(): bool
    {
        return $this->status === AppointmentStatus::Confirmed;
    }

    /**
     * Un RDV est annulable tant qu'il est actif et à venir.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, AppointmentStatus::activeStatuses(), true)
            && $this->date->greaterThanOrEqualTo(today());
    }

    public function startDateTime(): Carbon
    {
        return $this->date->copy()->setTimeFromTimeString($this->start_time);
    }
}
