<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;

/**
 * Field upload yang sudah diatur: disk "uploads" (public/uploads), tipe & ukuran dibatasi.
 */
class Upload
{
    /** Batas ukuran gambar dalam KB. */
    public const IMAGE_MAX_KB = 2048;

    /** Batas ukuran PDF (CV) dalam KB — PRD A2: maks. 5 MB. */
    public const PDF_MAX_KB = 5120;

    public static function image(string $name, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->disk('uploads')
            ->directory($directory)
            ->visibility('public')
            ->image()
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->maxSize(self::IMAGE_MAX_KB)
            ->helperText('Format JPG, PNG, atau WebP. Maksimal 2 MB.');
    }

    public static function pdf(string $name, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->disk('uploads')
            ->directory($directory)
            ->visibility('public')
            ->acceptedFileTypes(['application/pdf'])
            ->maxSize(self::PDF_MAX_KB)
            ->helperText('Format PDF. Maksimal 5 MB.');
    }
}
