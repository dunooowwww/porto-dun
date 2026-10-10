# CLAUDE.md – Portofolio Dun #12

## Ringkasan proyek
Website portofolio pribadi one-page milik Dun (mahasiswa Sistem Informasi), tema **F1 tipis-tipis**
(80% minimalis, 20% racing), nomor balap **#12**, dengan maskot karakter anime. Konten (profil, timeline,
project, kontak, pesan maskot) dikelola lewat admin panel Filament di `/admin`.
Dun masih belajar: jelaskan setiap langkah dengan bahasa sederhana (Bahasa Indonesia).

## Dokumen WAJIB diikuti
- `_bahan/PRD.md` — spesifikasi produk: fitur, admin, model data, deploy, milestone (M1–M9).
- `_bahan/design.md` — aturan visual, termasuk daftar **DILARANG** (anti AI slop) di bagian 7.
  Baca sebelum menulis UI apa pun. Kalau ragu, ikuti design.md.
- `_bahan/konten.md` — isi awal website, sumber data seeder.

Folder `_bahan/` adalah bahan mentah: **jangan dihapus atau diubah**.
Kerjakan per milestone sesuai PRD bagian 11; jangan loncat ke milestone berikutnya tanpa diminta.

## Stack
- Laravel 13 (butuh **PHP ≥ 8.3**), Blade + komponen Blade
- Tailwind CSS v4 via Vite (`@tailwindcss/vite`), Alpine.js
- GSAP hanya untuk loading screen & animasi scroll (mulai M7)
- Filament (versi stabil terbaru) untuk admin (mulai M3)
- Database: SQLite di lokal (`database/database.sqlite`), MySQL di hosting
- Font self-hosted via npm `@fontsource-variable/*`: Archivo (heading), Inter (body), JetBrains Mono (label)
- Ikon: Lucide/Tabler SVG inline, stroke 1.5. Tidak boleh emoji sebagai ikon.

## Token desain (satu sumber)
Semua warna, font, dan radius didefinisikan di blok `@theme` pada `resources/css/app.css`.
Palet bawaan Tailwind dimatikan (`--color-*: initial`), jadi hanya kelas ini yang ada:
- Warna: `bg`, `card`, `ink`, `muted`, `line`, `red`, `red-dark` → mis. `bg-bg`, `text-muted`, `border-line`
- Font: `font-display` (Archivo), `font-sans` (Inter), `font-mono` (JetBrains Mono)
- Radius: `rounded-card` (24px), `rounded-pill` (999px), `rounded-chip` (12px)
- Lainnya: `max-w-site` (1200px), `tracking-display`, `tracking-label`, `ease-out-race`
- Utilitas/komponen: `label-mono`, `btn-primary` (+ `btn-dot`), `btn-secondary`

Jangan hardcode hex, nama font, atau radius di Blade. Teks kecil merah pakai `text-red-dark`.

## Struktur penting
- `resources/views/components/layouts/app.blade.php` — layout dasar (`<x-layouts.app title="..." description="...">`)
- `resources/views/design-test.blade.php` — halaman tes M1 di `/` (diganti Home di M4)
- `public/images/` — gambar maskot, PNG asli + WebP (pakai WebP di `<img>`):
  `hero`, `ai-ml`, `builder`, `team`, `strategy`, `race`, `garage`, `contact`, `dnf`, `mascot-head`
- `public/uploads/` — tempat file upload dari admin (di-ignore git)

## Perintah penting
```bash
composer install && npm install          # pasang dependensi
cp .env.example .env && php artisan key:generate   # sekali, kalau .env belum ada
touch database/database.sqlite && php artisan migrate
composer run dev     # server Laravel + Vite (hot reload) sekaligus
php artisan serve    # hanya server Laravel → http://127.0.0.1:8000 (butuh npm run build dulu)
npm run dev          # Vite dev server (hot reload CSS/JS)
npm run build        # build aset produksi ke public/build
php artisan test     # jalankan test
php artisan migrate:fresh --seed   # reset DB + isi data awal (mulai M2)
```

## Catatan shared hosting (wajib diingat sejak awal)
- **Server tidak punya Node.js.** Aset di-build di lokal (`npm run build`) dan folder `public/build`
  ikut di-deploy. Jangan pakai fitur yang butuh Node/SSR di server.
- **Upload disimpan di `public/uploads`** lewat disk khusus (dibuat di M2/M3), **tanpa `storage:link`**
  karena banyak shared hosting memblokir symlink. Jangan pakai disk `public` bawaan Laravel.
- Tanpa queue worker / cron wajib. Email dikirim sinkron; kalau SMTP gagal, data tetap tersimpan.
- Hosting harus PHP ≥ 8.3 (syarat Laravel 13) dengan ekstensi `intl`, `zip`, `gd`.
- Produksi: `APP_ENV=production`, `APP_DEBUG=false`. Langkah deploy lengkap ada di PRD bagian 10.

## Definition of Done tiap milestone
Ikuti design.md (termasuk DILARANG), cek di lebar 360px dan 1280px, tidak ada error di console
browser maupun `storage/logs/laravel.log`, dan `php artisan test` lulus.
