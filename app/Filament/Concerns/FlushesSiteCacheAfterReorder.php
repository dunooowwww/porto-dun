<?php

namespace App\Filament\Concerns;

use App\Support\SiteCache;

/**
 * Drag & drop di tabel Filament menyimpan urutan lewat query langsung (tanpa event model),
 * jadi cache halaman depan dibersihkan manual di sini.
 */
trait FlushesSiteCacheAfterReorder
{
    public function reorderTable(array $order, int|string|null $draggedRecordKey = null): void
    {
        parent::reorderTable($order, $draggedRecordKey);

        SiteCache::flush();
    }
}
