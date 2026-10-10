<?php

namespace App\Models\Concerns;

/**
 * Data baru otomatis diletakkan di urutan paling akhir jika kolom "sort" belum diisi.
 * Urutan selanjutnya diatur lewat drag & drop di tabel admin.
 */
trait HasSortOrder
{
    protected static function bootHasSortOrder(): void
    {
        static::creating(function (self $model) {
            if (! $model->sort) {
                $model->sort = (int) static::max('sort') + 1;
            }
        });
    }
}
