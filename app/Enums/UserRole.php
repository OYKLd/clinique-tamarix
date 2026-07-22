<?php

namespace App\Enums;

enum UserRole: string
{
    case Accueil = 'accueil';
    case Medecin = 'medecin';
    case Administration = 'administration';
    case Direction = 'direction';

    public function label(): string
    {
        return match ($this) {
            self::Accueil => 'Accueil',
            self::Medecin => 'Médecin',
            self::Administration => 'Administration',
            self::Direction => 'Direction',
        };
    }

    /**
     * Rôles autorisés à gérer les autres utilisateurs.
     */
    public function canManageUsers(): bool
    {
        return in_array($this, [self::Administration, self::Direction], true);
    }

    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn (self $role) => $role->label(), self::cases()),
        );
    }
}
