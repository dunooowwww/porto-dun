<?php

namespace App\Models;

use App\Support\SiteCache;
use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Pengaturan situs berbentuk key → value (JSON). Baca lewat helper setting('key').
 */
#[Fillable(['key', 'value'])]
class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory;

    public const CACHE_KEY = 'site_settings';

    protected function casts(): array
    {
        return [
            'value' => 'json',
        ];
    }

    protected static function booted(): void
    {
        // Setiap ada perubahan, buang cache supaya setting() membaca nilai terbaru.
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }

    /**
     * Semua setting sebagai array [key => value], diambil dari cache.
     *
     * @return array<string, mixed>
     */
    public static function allCached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()
            ->get()
            ->mapWithKeys(fn (self $setting) => [$setting->key => $setting->value])
            ->all());
    }

    public static function set(string $key, mixed $value): self
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        SiteCache::flush();
    }
}
