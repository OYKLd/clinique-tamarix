<?php

namespace App\Enums;

enum NotificationStatus: string
{
    case Queued = 'queued';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Read = 'read';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Queued => 'En file',
            self::Sent => 'Envoyé',
            self::Delivered => 'Délivré',
            self::Read => 'Lu',
            self::Failed => 'Échec',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Queued => 'text-bg-secondary',
            self::Sent => 'text-bg-info',
            self::Delivered => 'text-bg-primary',
            self::Read => 'text-bg-success',
            self::Failed => 'text-bg-danger',
        };
    }
}
