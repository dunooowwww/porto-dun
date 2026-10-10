<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Hapus file lama di disk "uploads" saat diganti atau saat datanya dihapus,
 * supaya kuota shared hosting tidak penuh oleh file yang tidak terpakai.
 * Filament sendiri tidak menghapus file lama.
 */
trait DeletesUploadedFiles
{
    /**
     * Kolom yang berisi path file di disk "uploads".
     *
     * @return array<int, string>
     */
    abstract protected function uploadAttributes(): array;

    protected static function bootDeletesUploadedFiles(): void
    {
        static::updated(function (self $model) {
            foreach ($model->uploadAttributes() as $attribute) {
                if ($model->wasChanged($attribute)) {
                    static::deleteUpload($model->getOriginal($attribute));
                }
            }
        });

        static::deleted(function (self $model) {
            foreach ($model->uploadAttributes() as $attribute) {
                static::deleteUpload($model->getAttribute($attribute));
            }
        });
    }

    protected static function deleteUpload(?string $path): void
    {
        if (filled($path)) {
            Storage::disk('uploads')->delete($path);
        }
    }
}
