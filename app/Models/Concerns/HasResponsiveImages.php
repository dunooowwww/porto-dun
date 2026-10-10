<?php

namespace App\Models\Concerns;

use App\Support\ImageVariants;

/**
 * Buat varian ukuran (srcset) otomatis saat gambar baru disimpan.
 * Varian lama ikut dihapus oleh DeletesUploadedFiles.
 */
trait HasResponsiveImages
{
    protected static function bootHasResponsiveImages(): void
    {
        static::saved(function (self $model) {
            foreach ($model->uploadAttributes() as $attribute) {
                if ($model->wasRecentlyCreated || $model->wasChanged($attribute)) {
                    ImageVariants::generate($model->getAttribute($attribute));
                }
            }
        });
    }
}
