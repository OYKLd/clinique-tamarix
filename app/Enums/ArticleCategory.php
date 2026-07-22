<?php

namespace App\Enums;

enum ArticleCategory: string
{
    case ConseilSante = 'conseil-sante';
    case Actualite = 'actualite';
    case Communique = 'communique';

    public function label(): string
    {
        return match ($this) {
            self::ConseilSante => 'Conseil santé',
            self::Actualite => 'Actualité',
            self::Communique => 'Communiqué',
        };
    }

    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn (self $category) => $category->label(), self::cases()),
        );
    }
}
