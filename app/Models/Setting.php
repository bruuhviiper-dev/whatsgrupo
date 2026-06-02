<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }

    public static function adsenseEnabled(): bool
    {
        return (bool) static::get('adsense_enabled', false);
    }

    public static function adsenseScript(): ?string
    {
        return static::get('adsense_script');
    }

    public static function adsenseMetaTag(): ?string
    {
        return static::get('adsense_meta_tag');
    }

    public static function adsenseClientId(): ?string
    {
        return static::get('adsense_client_id');
    }

    // ── Google Analytics (GA4) ──────────────────────────────────────────────
    public static function googleAnalyticsEnabled(): bool
    {
        return (bool) static::get('ga_enabled', false);
    }

    /** ID de medição GA4 (ex: G-XXXXXXXXXX). */
    public static function googleAnalyticsId(): ?string
    {
        return static::get('ga_measurement_id');
    }

    /** Script completo colado pelo admin (tem prioridade sobre o ID). */
    public static function googleAnalyticsScript(): ?string
    {
        return static::get('ga_script');
    }
}
