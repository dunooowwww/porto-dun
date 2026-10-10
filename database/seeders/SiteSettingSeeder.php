<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * Pengaturan Situs dari _bahan/konten.md.
 *
 * Aturan isi:
 * - Teks gaya/copywriting yang punya contoh di konten.md → memakai contoh tersebut.
 * - Data faktual yang belum diisi (nama lengkap, kota) → "[ISI NANTI]".
 * - Link, email, nomor, dan file yang belum ada → null (dikosongkan, bukan teks placeholder,
 *   supaya tidak menghasilkan link rusak). Tampilan akan menyembunyikan yang kosong.
 */
class SiteSettingSeeder extends Seeder
{
    public const TODO = '[ISI NANTI]';

    public function run(): void
    {
        foreach ($this->settings() as $key => $value) {
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        SiteSetting::flushCache();
    }

    /**
     * @return array<string, mixed>
     */
    public function settings(): array
    {
        return [
            // Tab Profil (konten.md bagian 0 & 1)
            'display_name' => 'Dun',
            'full_name' => self::TODO,
            'racing_number' => 12,
            'role' => 'Information Systems Student & AI Builder',
            'city' => self::TODO,
            'hero_greeting' => 'Halo, aku Dun',
            'hero_tagline' => 'Mahasiswa SI di siang hari, ngulik AI sampai lights out.',
            'hero_description' => 'Aku suka ngubah ide jadi produk yang bisa dipakai — dari analisis kebutuhan sampai deploy.',
            'hero_scroll_text' => 'Lights out, scroll down ↓',
            // Daftar tech = data faktual tentang skill, jadi dikosongkan sampai Dun mengisinya.
            // Format tiap item: ['name' => 'Laravel', 'icon' => 'nama-ikon'].
            'hero_tech_icons' => [],

            // Tab Section (konten.md bagian 2, 3, 4, 6)
            'pit_wall_title' => 'Pit Wall',
            'pit_wall_intro' => 'Ini yang bikin aku beda di lintasan.',
            'race_title' => 'Race Progress',
            'race_intro' => 'Lap demi lap perjalananku sejauh ini.',
            'garage_title' => 'Garage',
            'garage_intro' => 'Mesin-mesin yang pernah aku rakit.',
            'contact_title' => 'Box, box! Yuk ngobrol',
            'contact_intro' => 'Punya ide project, lomba, atau tawaran magang? Gas ngobrol.',

            // Tab Kontak (konten.md bagian 6)
            'contact_email' => null,
            'contact_whatsapp' => null, // format 62xxx
            'contact_linkedin' => null,
            'contact_github' => null,
            'contact_instagram' => null,
            'contact_quote' => '"Kalau chat belum dibales, berarti lagi debugging."',

            // Tab File
            'cv_path' => null, // path di disk "uploads", contoh: files/cv-dun.pdf
            'og_image' => null,

            // Tab SEO & Footer (konten.md bagian 0 & 8)
            'meta_title' => 'Dun #12 – Portofolio',
            'meta_description' => 'Portofolio Dun, mahasiswa Sistem Informasi yang suka bikin produk berbasis AI.',
            'footer_text' => 'Dirakit dengan kopi dan semangat race day.',
            'not_found_title' => 'DNF – Did Not Find',
            'not_found_text' => 'Halaman ini mogok di tikungan. Balik ke pit yuk.',
        ];
    }
}
