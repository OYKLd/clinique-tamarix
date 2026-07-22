<?php

namespace App\Enums;

enum NotificationChannel: string
{
    case Whatsapp = 'whatsapp';
    case Sms = 'sms';
    case Mail = 'mail';

    public function label(): string
    {
        return match ($this) {
            self::Whatsapp => 'WhatsApp',
            self::Sms => 'SMS',
            self::Mail => 'E-mail',
        };
    }
}
