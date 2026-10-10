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
- Animasi (M7) memakai CSS + sedikit JS, **tanpa GSAP** (hemat ±27 KB gzip demi Lighthouse). Jangan tambah library animasi.
- Filament v5 (Livewire 4) untuk admin di `/admin`
- Database: SQLite di lokal (`database/database.sqlite`), MySQL di hosting
- Font self-hosted via npm `@fontsource-variable/*`: Archivo (heading), Inter (body), JetBrains Mono (label)
- Ikon: Tabler (`secondnetwork/blade-tabler-icons`) lewat `<x-site.icon name="arrow-right" class="size-4" />`
  → stroke 1.5, aria-hidden, nama tak dikenal tidak dirender. JANGAN pakai `<x-icon>` (milik paket blade-icons)
  atau `<x-tabler-*>` langsung. Tidak boleh emoji sebagai ikon.

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
- `resources/views/components/layouts/app.blade.php` — layout utama: meta SEO/OG dari `setting()` (bisa ditimpa
  lewat props `title`, `description`, `image`), skip link, slot `header`, `<main id="konten">`
- `resources/views/home.blade.php` + `app/Http/Controllers/HomeController.php` — halaman depan
- `App\Support\FrontData` — data publik (pitWall, timeline, garage) sebagai array, ter-cache lewat `SiteCache`.
  Posisi P1, P2, … dan navigasi sebelumnya/berikutnya mengikuti urutan `FrontData::garage()`.
- `resources/views/projects/show.blade.php` + `ProjectController` — detail `/projects/{slug}` (route `projects.show`);
  draft hanya untuk admin login (+ `noindex`), slug salah → 404
- `resources/views/components/site/` — `navbar` (Alpine `navbar()`), `footer` (di layout, semua halaman),
  `mascot` (Alpine `mascot()`, hanya halaman depan lewat slot `floating`), `icon`
- `resources/views/errors/404.blade.php` — halaman "DNF – Did Not Find" (teks dari Pengaturan Situs)
- `resources/views/components/home/race.blade.php` — chart Race Strategy: tab Alpine `raceTabs` (pola tab ARIA)
  "Main race · Profesional" / "Sprint · Organisasi", data dari `FrontData::raceStrategy()` (rentang tahun & posisi bar
  dalam persen per tab, cache per bulan). Desktop = chart, HP = kartu vertikal; mobil kecil = komponen `race-car`.
  Tiap stint bisa diklik → modal detail `home/stint-dialog` (`<dialog>` bawaan, dibuka `raceTabs.openStint(id)`,
  id `stint-{experience id}`; Esc / klik backdrop / tombol X menutup).
- `resources/views/components/home/` — `hero`, `hero-name`, `pit-wall`, `race`, `garage`, `box-box`
  (+ `field-error`, `contact-success`), `section-heading`
- `App\Support\Sections::NAV` — id section ↔ label menu (dipakai navbar & maskot)
- `App\Support\HeroName::split()` — bagian sapaan hero yang diberi garis merah + #12 (nama hanya tertulis sekali):
  `*bintang*` → nama tampil di dalam sapaan → kata terakhir. Jangan menambahkan `display_name` terpisah di H1.
- `public/images/` — gambar maskot, PNG asli + WebP (pakai WebP di `<img>`):
  `hero`, `ai-ml`, `builder`, `team`, `strategy`, `race`, `garage`, `contact`, `dnf`, `mascot-head`
- `public/uploads/` — tempat file upload dari admin (di-ignore git)

## Data (M2)
- Model di `app/Models`: `SiteSetting`, `PitWallCard`, `Experience`, `Project` (+ `ProjectImage`), `MascotMessage`, `ContactMessage`.
- Enum di `app/Enums`: `ExperienceType` (Pendidikan…Sertifikasi), `ProjectStatus` (draft/published) — pakai `::options()` untuk dropdown.
- `setting('key', $default)` (di `app/helpers.php`) membaca `site_settings` dari cache; cache otomatis dibuang
  saat `SiteSetting` disimpan/dihapus. Ubah setting lewat `SiteSetting::set('key', $value)`, bukan query mentah.
- File upload: simpan path relatif di disk `uploads` (contoh `pit-wall/ai-ml.webp`), tampilkan dengan `upload_url($path)`
  atau accessor model (`image_url`, `thumbnail_url`, `logo_url`, `url`). URL disk sengaja relatif (`/uploads/...`),
  tidak bergantung `APP_URL`; untuk URL absolut (OG image) bungkus dengan `url()`.
- Gambar responsif: trait `HasResponsiveImages` membuat varian WebP 480/960/1600px (GD) saat gambar disimpan;
  pakai `srcset="{{ upload_srcset($path) }}"` + `sizes`. Gambar lama: `php artisan images:variants`.
- `Experience`: `duration_label` ("1 thn 3 bln", bulan kalender inklusif), `period_label` ("Agt 2023 – sekarang").
- `Experience.category` (`ExperienceCategory`: professional/organizational) untuk chart Race Strategy.
  `ExperienceType::defaultCategory()`: Magang/Kerja → professional, Organisasi/Kepanitiaan → organizational,
  Pendidikan/Lomba/Sertifikasi → null (tidak tampil di chart). Form admin mengisi kategori otomatis dari jenis.
- `Project`: slug otomatis & unik dari judul, `published_at` terisi saat pertama terbit, scope `published()`.
- Seeder mengisi dari `_bahan/konten.md`. Data faktual yang belum ada = `[ISI NANTI]`, link/email/tanggal/angka = null.
  Jangan mengarang data. Seeder aman dijalankan ulang (tidak menimpa editan admin).
- Data asli dari CV & portofolio ada di `CvContentSeeder` (dipanggil `DatabaseSeeder` sebelum Experience/ProjectSeeder):
  kontak, CV, 12 pengalaman, 7 project + galeri, teks kartu Pit Wall. File-nya (CV PDF, screenshot WebP) di
  `database/seeders/files/` dan disalin ke disk `uploads`. Menghapus placeholder `[ISI NANTI]`; pengaturan hanya diisi
  kalau masih kosong/placeholder/nilai bawaan. Di hosting cukup `php artisan db:seed --force`.
  Catatan: project/pengalaman bawaan yang dihapus di admin akan muncul lagi kalau seeder dijalankan ulang.

## Admin Filament (M3)
- Panel: `app/Providers/Filament/AdminPanelProvider.php` — brand "Dun #12", palet merah manual (600 = #E10600,
  700 = #B80500; jangan pakai `Color::hex()` karena hasilnya pucat), font Inter lokal, UI Bahasa Indonesia (`APP_LOCALE=id`).
- Resource di `app/Filament/Resources/*` (struktur v5: `Schemas/`, `Tables/`, `Pages/`).
  Pengaturan Situs = halaman custom `app/Filament/Pages/ManageSiteSettings.php` (5 tab, simpan via `SiteSetting::set`).
- Upload: selalu pakai `App\Filament\Support\Upload::image()` / `::pdf()` → disk `uploads`, JPG/PNG/WebP ≤ 2 MB, PDF ≤ 5 MB.
  `ImageColumn` wajib `->disk('uploads')`.
- File lama dihapus otomatis lewat trait `DeletesUploadedFiles` di model (Filament sendiri tidak menghapusnya).
- Cache halaman depan: pakai `App\Support\SiteCache::remember('key', fn () => ...)`. **Naikkan `SiteCache::SCHEMA`**
  setiap kali bentuk array FrontData berubah (key baru), kalau tidak cache lama bikin error "Undefined array key". Model konten memakai trait
  `FlushesSiteCache`; halaman list yang bisa drag & drop memakai `App\Filament\Concerns\FlushesSiteCacheAfterReorder`
  (reorder Filament tidak memicu event model).
- `User` mengimplementasikan `FilamentUser::canAccessPanel()` — wajib agar /admin bisa diakses di production.
- Pesan validasi Indonesia ada di `lang/id/validation.php`.
- Kolom `projects.process` berisi HTML dari RichEditor → selalu tampilkan dengan
  `{!! str($project->process)->sanitizeHtml() !!}` di dalam `.prose-case`. Teks biasa: `{!! nl2br(e($teks)) !!}`.

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
php artisan migrate:fresh --seed   # reset DB + isi data awal (menghapus akun admin juga!)
php artisan make:filament-user     # buat akun admin (interaktif)
vendor/bin/pint                    # rapikan format kode PHP
```

## Catatan shared hosting (wajib diingat sejak awal)
- **Server tidak punya Node.js.** Aset di-build di lokal (`npm run build`) dan folder `public/build`
  ikut di-deploy. Jangan pakai fitur yang butuh Node/SSR di server.
- **Upload disimpan di `public/uploads`** lewat disk khusus (dibuat di M2/M3), **tanpa `storage:link`**
  karena banyak shared hosting memblokir symlink. Jangan pakai disk `public` bawaan Laravel.
- Aset Filament (`public/css|js|fonts/filament`) tidak masuk git; dibuat ulang otomatis oleh `composer install`
  (script `filament:upgrade`). Kalau upload manual tanpa Composer di server, ikutkan folder-folder itu.
- Tanpa queue worker / cron wajib. Email dikirim sinkron; kalau SMTP gagal, data tetap tersimpan.
  Untuk mengaktifkan email: isi `MAIL_MAILER=smtp` + `MAIL_HOST/PORT/USERNAME/PASSWORD/FROM_ADDRESS` di `.env`
  dan isi Email di admin → Pengaturan Situs → Kontak.
- Hosting harus PHP ≥ 8.3 (syarat Laravel 13) dengan ekstensi `intl`, `zip`, `gd`.
- Produksi: `APP_ENV=production`, `APP_DEBUG=false`. Langkah deploy lengkap ada di PRD bagian 10.

## Form kontak (M6)
- `POST /kontak` → `ContactMessageController@store` + `StoreContactMessageRequest`. Tanpa JS: redirect ke `/#box-box`
  dengan flash `contact_sent`; dengan Alpine `contactForm()`: fetch + JSON (422 untuk error validasi).
- Honeypot field `website`: kalau terisi, respons tetap sukses tapi pesan TIDAK disimpan.
- Rate limit manual 3 kiriman berhasil / 10 menit per IP (`RateLimiter`, key `contact:{ip}`); gagal validasi tidak dihitung.
  Di hosting di balik proxy/Cloudflare, atur `trustProxies` agar `$request->ip()` benar.
- Email ke `setting('contact_email')` hanya jika `MAIL_MAILER` bukan `log`/`array`; error kirim → `report()` saja.

## SEO, performa, aksesibilitas (M8)
- `/sitemap.xml` & `/robots.txt` dinamis (`SeoController`); jangan buat file statis `public/robots.txt` (akan menang).
- Favicon dari kepala maskot: `public/favicon.ico`, `images/favicon-32.png`, `images/apple-touch-icon.png`.
  OG default `images/og-default.jpg` (1200×630). Widget maskot memakai `images/mascot-head-160.webp`.
- Layout mem-preload font Archivo & Inter (latin) lewat `Vite::asset(...)`.
- `public/.htaccess` berisi kompresi (mod_deflate) & cache 1 tahun untuk /build (nama ber-hash).
- Ikon `brand-*` (logo merek asli) DILARANG untuk konten: `Icon::allowedForContent()` + validasi admin.
- Item grid yang berisi teks panjang (URL) wajib `min-w-0`, kalau tidak HP 360px bisa scroll horizontal.
- Test wajib PRD §12 ada di `tests/Feature/PrdAcceptanceTest.php`.

## Catatan front-end
- Fokus keyboard: aturan `:focus-visible` sengaja di luar `@layer` di app.css (utilitas Tailwind v4 menang atas
  layer base). Jangan pindahkan ke `@layer base`.
- Section memakai `scroll-mt-24` agar judul tidak tertutup navbar; link navbar berbentuk `url('/').'#id'`.
- Jarak section: `py-16 md:py-24`. Latar berselang-seling: hero/race/box-box `bg` (off-white),
  pit-wall/garage `bg-card border-y border-line` (putih) — kartu di section putih memakai `bg-bg`.
- Gambar upload: beri `width`/`height` (helper `upload_image_size($path)`) dan `loading="lazy"` kecuali hero.

## Motion (M7) — hanya animasi ini, jangan menambah yang lain
- Loading screen "Lights Out": `components/site/lights-script` (skrip inline di `<head>`, slot `head`) + `lights-out`
  (overlay, slot `overlay`). Urutan lampu murni CSS (`.lights-on` di `<html>`); sekali per sesi (`sessionStorage`),
  skip klik/Esc, dilewati jika reduced-motion. Tanpa JS kelas tak pernah dipasang → halaman tidak terkunci.
  Keyframe overlay menulis `visibility`/`pointer-events` eksplisit di 0–80% (properti diskrit berganti di tengah keyframe).
- Animasi masuk: beri atribut `data-reveal` (+ `style="--reveal-delay: {{ min($i, 6) * 60 }}ms"`). `.reveal-ready`
  dipasang oleh `initReveal()` di app.js, jadi tanpa JS konten tetap tampil. Hero sengaja tidak ikut.
- Hover kartu: `transition duration-300 ease-out-race hover:-translate-y-1 hover:border-muted`.
  Link: utilitas `link-grow` (garis bawah merah tumbuh dari kiri; bisa dipicu `.group:hover`).
- Mobil di lintasan Race: Alpine `raceCar` pada `<ol>`, gerak lewat properti CSS `translate` (bukan `transform`,
  karena di HP mobil diputar dengan `rotate`).
- Bendera sukses form: kelas `flag-wave`.
- Semua animasi besar dimatikan di blok `@media (prefers-reduced-motion: reduce)` di app.css + cek di JS.

## Definition of Done tiap milestone
Ikuti design.md (termasuk DILARANG), cek di lebar 360px dan 1280px, tidak ada error di console
browser maupun `storage/logs/laravel.log`, dan `php artisan test` lulus.
