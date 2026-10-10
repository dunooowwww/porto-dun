<?php

use App\Models\SiteSetting;
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
