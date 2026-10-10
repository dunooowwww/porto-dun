<?php

namespace App\Models\Concerns;

use App\Support\SiteCache;

/**
 * Setiap konten disimpan/dihapus (misalnya lewat admin), cache halaman depan dibersihkan (PRD A0).
 */
trait FlushesSiteCache
{
    protected static function bootFlushesSiteCache(): void
    {
        static::saved(fn () => SiteCache::flush());
        static::deleted(fn () => SiteCache::flush());
    }
}
