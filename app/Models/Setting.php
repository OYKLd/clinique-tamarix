<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Lit un paramètre (mis en cache 1 h).
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $settings = Cache::remember('settings.all', 3600, fn () => self::pluck('value', 'key')->all());

        return $settings[$key] ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('settings.all');
    }
}
