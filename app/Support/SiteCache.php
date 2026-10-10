<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Cache untuk data halaman depan (dipakai mulai M4).
 *
 * Memakai nomor versi: flush() cukup menaikkan versi sehingga semua key lama otomatis tidak terpakai.
 * Cara ini jalan di semua cache store (termasuk "database" di shared hosting) tanpa butuh cache tags.
 */
class SiteCache
{
    public const VERSION_KEY = 'site_cache_version';

    public static function remember(string $key, Closure $callback): mixed
    {
        return Cache::rememberForever(static::key($key), $callback);
    }

    public static function key(string $key): string
    {
        return 'site:v'.static::version().':'.$key;
    }

    public static function version(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 1);
    }

    public static function flush(): void
    {
        Cache::forever(self::VERSION_KEY, static::version() + 1);
    }
}
