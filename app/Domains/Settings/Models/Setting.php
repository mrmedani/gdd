<?php

namespace App\Domains\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    private static ?array $cache = null;

    public static function get(string $key, mixed $default = null): mixed
    {
        if (self::$cache === null) {
            self::$cache = Cache::remember('settings.all', 3600, function () {
                $all = [];
                foreach (static::all() as $setting) {
                    $decoded = json_decode($setting->value, true);
                    $all[$setting->key] = json_last_error() === JSON_ERROR_NONE ? $decoded : $setting->value;
                }
                return $all;
            });
        }
        return self::$cache[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value]);
        self::$cache = null;
        Cache::forget('settings.all');
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings.all'));
        static::deleted(fn () => Cache::forget('settings.all'));
    }

    public static function flushCache(): void
    {
        self::$cache = null;
        Cache::forget('settings.all');
    }
}
