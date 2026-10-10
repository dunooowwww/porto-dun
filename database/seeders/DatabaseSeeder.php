<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Isi data awal dari _bahan/konten.md + data asli dari CV & portofolio (CvContentSeeder).
     *
     * Aman dijalankan ulang: data yang sudah diubah lewat admin tidak ditimpa.
     * Akun admin TIDAK dibuat di sini — pakai `php artisan make:filament-user` (mulai M3).
     */
    public function run(): void
    {
        $this->call([
            SiteSettingSeeder::class,
            PitWallCardSeeder::class,
            // Sebelum Experience/ProjectSeeder: tabel sudah terisi data asli, jadi placeholder tidak dibuat.
            CvContentSeeder::class,
            ExperienceSeeder::class,
            ProjectSeeder::class,
            MascotMessageSeeder::class,
        ]);
    }
}
