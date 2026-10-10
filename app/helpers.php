<?php

use App\Models\SiteSetting;
use App\Support\ImageVariants;
use Illuminate\Support\Facades\Storage;

if (! function_exists('setting')) {
    /**
     * Ambil nilai Pengaturan Situs, contoh: setting('hero_tagline').
     * Semua setting dibaca sekali lalu disimpan di cache; cache dibuang otomatis saat setting disimpan.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        $value = SiteSetting::allCached()[$key] ?? null;

        return $value ?? $default;
    }
}

if (! function_exists('upload_url')) {
    /**
     * URL publik untuk file di disk "uploads" (public/uploads). Null jika path kosong.
     */
    function upload_url(?string $path): ?string
    {
        return $path ? Storage::disk('uploads')->url($path) : null;
    }
}

if (! function_exists('upload_image_size')) {
    /**
     * Ukuran asli gambar di disk "uploads" untuk atribut width/height (mencegah layout bergeser).
     *
     * @return array{0: int, 1: int}|null
     */
    function upload_image_size(?string $path): ?array
    {
        if (blank($path) || ! Storage::disk('uploads')->exists($path)) {
            return null;
        }

        $size = @getimagesize(Storage::disk('uploads')->path($path));

        return $size ? [$size[0], $size[1]] : null;
    }
}

if (! function_exists('upload_srcset')) {
    /**
     * Atribut srcset untuk gambar upload (varian 480/960/1600px). Null jika belum ada varian.
     */
    function upload_srcset(?string $path): ?string
    {
        return ImageVariants::srcset($path);
    }
}
