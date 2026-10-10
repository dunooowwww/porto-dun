<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Varian ukuran gambar upload untuk srcset (PRD §9: gambar WebP & ukuran responsif).
 *
 * Contoh: projects/abc.jpg (3000px) → projects/abc-480w.webp, -960w.webp, -1600w.webp.
 * Memakai GD (tersedia di shared hosting). File asli tidak diubah; gagal membuat varian
 * tidak boleh menggagalkan penyimpanan di admin.
 */
class ImageVariants
{
    /** @var array<int, int> */
    public const WIDTHS = [480, 960, 1600];

    public const QUALITY = 82;

    public static function generate(?string $path): void
    {
        $disk = Storage::disk('uploads');

        if (! static::isImage($path) || ! $disk->exists($path) || ! function_exists('imagewebp')) {
            return;
        }

        try {
            $source = @imagecreatefromstring($disk->get($path));

            if ($source === false) {
                return;
            }

            $width = imagesx($source);
            $height = imagesy($source);

            foreach (self::WIDTHS as $target) {
                if ($target >= $width) {
                    continue;
                }

                $targetHeight = (int) round($height * $target / $width);
                $resized = imagecreatetruecolor($target, $targetHeight);
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $target, $targetHeight, $width, $height);

                ob_start();
                imagewebp($resized, null, self::QUALITY);
                $disk->put(static::variantPath($path, $target), ob_get_clean());
                imagedestroy($resized);
            }

            imagedestroy($source);
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * Nilai atribut srcset, mis. "/uploads/a-480w.webp 480w, /uploads/a.jpg 1200w". Null jika tidak ada varian.
     */
    public static function srcset(?string $path): ?string
    {
        if (! static::isImage($path)) {
            return null;
        }

        $disk = Storage::disk('uploads');
        $candidates = [];

        foreach (self::WIDTHS as $width) {
            $variant = static::variantPath($path, $width);

            if ($disk->exists($variant)) {
                $candidates[] = $disk->url($variant).' '.$width.'w';
            }
        }

        if ($candidates === []) {
            return null;
        }

        $size = upload_image_size($path);

        if ($size) {
            $candidates[] = $disk->url($path).' '.$size[0].'w';
        }

        return implode(', ', $candidates);
    }

    public static function delete(?string $path): void
    {
        if (! static::isImage($path)) {
            return;
        }

        foreach (self::WIDTHS as $width) {
            Storage::disk('uploads')->delete(static::variantPath($path, $width));
        }
    }

    public static function variantPath(string $path, int $width): string
    {
        $info = pathinfo($path);
        $dir = ($info['dirname'] ?? '.') === '.' ? '' : $info['dirname'].'/';

        return $dir.$info['filename'].'-'.$width.'w.webp';
    }

    protected static function isImage(?string $path): bool
    {
        return filled($path) && in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true);
    }
}
