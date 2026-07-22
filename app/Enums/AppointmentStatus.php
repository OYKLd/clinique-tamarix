<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Confirmed => 'Confirmé',
            self::Completed => 'Honoré',
            self::Cancelled => 'Annulé',
        };
    }

    /**
     * Classe de badge Bootstrap associée au statut.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'text-bg-warning',
            self::Confirmed => 'text-bg-success',
            self::Completed => 'text-bg-secondary',
            self::Cancelled => 'text-bg-danger',
        };
    }

    /**
     * Statuts qui occupent réellement un créneau.
     */
    public static function activeStatuses(): array
    {
        return [self::Pending, self::Confirmed];
    }

    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn (self $status) => $status->label(), self::cases()),
        );
    }
}
