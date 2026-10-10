# PRD – Portofolio Dun #12

| | |
|---|---|
| Versi | 1.0 |
| Pemilik | Dun |
| Status | Siap dikerjakan |
| Dokumen terkait | `design.md` (aturan visual), `konten.md` (isi awal / data seeder) |

---

## 1. Ringkasan
Website portofolio pribadi one-page bertema **F1 tipis-tipis** (80% minimalis, 20% racing), mengacu struktur portoanam.com. Konten (profil, timeline, project, kontak, pesan maskot) dikelola lewat **admin panel Filament**, jadi update portofolio tidak perlu edit kode. Di-hosting di **shared hosting** (PHP + MySQL, tanpa Node.js di server).

## 2. Tujuan
1. Menampilkan siapa Dun, perjalanan, dan karya terbaiknya dengan cara yang mudah diingat (maskot #12 + tema racing).
2. Memudahkan recruiter / klien menghubungi (form + link kontak).
3. Dun bisa menambah / mengubah project dan timeline sendiri lewat admin dalam < 5 menit.

### Bukan tujuan (versi 1)
- Blog & halaman riset (masuk versi 2).
- Multi-bahasa (versi 2).
- Dark mode.
- Multi-user admin / role.

## 3. Target pengguna
| Pengguna | Kebutuhan |
|---|---|
| Recruiter magang/kerja | Cepat paham skill, peran di project, pengalaman; bisa download CV |
| Klien / partner project | Lihat hasil karya & cara kerja, kontak cepat (WA/email) |
| Teman / dosen | Lihat project & pencapaian |
| Dun (admin) | Kelola konten tanpa coding, baca pesan masuk |

## 4. Tech stack
| Bagian | Pilihan |
|---|---|
| Framework | Laravel versi stabil terbaru (12 atau lebih baru) |
| Template | Blade + komponen Blade |
| Styling | Tailwind CSS (via Vite) |
| Interaksi | Alpine.js; GSAP hanya untuk loading screen & animasi scroll |
| Admin panel | Filament versi stabil terbaru (v4 atau lebih baru) |
| Database | SQLite di lokal, MySQL di hosting |
| Ikon | Lucide / Tabler (SVG, stroke 1.5) |
| Hosting | Shared hosting: PHP ≥ 8.2, ekstensi `intl`, `zip`, `gd`, MySQL, SSH + Composer |

Aturan teknis:
- Aset frontend di-build di lokal (`npm run build`); folder `public/build` ikut di-deploy. Server tidak menjalankan Node.
- Tanpa queue worker / cron wajib. Email dikirim sinkron (atau dilewati kalau SMTP belum diatur).
- Upload gambar disimpan di `public/uploads` (disk khusus), **tidak bergantung pada `storage:link`**, karena banyak shared hosting memblokir symlink.

## 5. Halaman & route
| Route | Halaman | Keterangan |
|---|---|---|
| `/` | Home (one-page) | Semua section publik |
| `/projects/{slug}` | Detail project (case study) | Bisa dibagikan & terindeks Google |
| `/cv` | Redirect ke file CV | Hanya jika CV diunggah |
| `/admin` | Admin Filament | Login wajib |
| 404 | "DNF – Did Not Find" | Halaman error custom |

## 6. Fitur publik

### F1. Loading screen "Lights Out"
- 5 lampu merah menyala bergantian, lalu padam, lalu halaman tampil. Total ±1,5 detik.
- Hanya muncul sekali per sesi (sessionStorage, dibungkus try/catch). Bisa di-skip dengan klik / tombol Esc.
- Tidak muncul jika `prefers-reduced-motion`.
- **Diterima jika:** halaman tetap bisa dipakai walau JavaScript gagal (loading screen tidak boleh mengunci halaman).

### F2. Navbar
- Pill floating di tengah atas: Home, Pit Wall, Race, Garage, Box Box.
- Saat scroll ke bawah mengecil jadi tombol bulat ☰; menu aktif ditandai merah (scrollspy).
- Di HP: tombol ☰ membuka menu penuh.

### F3. Hero
- Sapaan, nama + garis bawah merah + label `#12`, tagline, deskripsi, deretan ikon tech, teks ajakan scroll.
- Gambar karakter hero di kanan (di HP pindah ke bawah teks).
- Tombol: "Lihat Garage" (utama, hitam) dan "Download CV" (sekunder, hanya jika CV ada).
- **Sumber data:** Pengaturan Situs.

### F4. Pit Wall (About)
- Judul + intro + bento grid 4 kartu ukuran tidak seragam.
- Tiap kartu: label `SECTOR n`, judul, isi singkat, gambar pose karakter.
- **Sumber data:** tabel `pit_wall_cards` (urutan bisa diatur).

### F5. Race Progress (Timeline)
- Lintasan horizontal (desktop) / vertikal (HP) dengan checkpoint per pengalaman.
- Tiap checkpoint: label `LAP 0n`, nama, peran, periode, jenis (badge), pencapaian.
- Durasi dihitung otomatis dari tanggal mulai–selesai ("1 thn 3 bln"); "sekarang" jika masih aktif.
- Gambar `race.png` sebagai visual section.
- **Sumber data:** tabel `experiences`.

### F6. Garage (Projects)
- Daftar project unggulan ala timing screen: posisi `P1, P2, …`, judul, deskripsi singkat, tech stack (tag), tahun, thumbnail, tombol "View Lap →" ke halaman detail.
- Hanya project berstatus terbit; urutan sesuai yang diatur di admin.
- **Sumber data:** tabel `projects`.

### F7. Detail project `/projects/{slug}`
- Header: judul, tahun, peran, tech stack, link GitHub & demo.
- Isi case study: Masalah → Peran & proses → Hasil (boleh angka) → Galeri screenshot.
- Navigasi "Project sebelumnya / berikutnya" dan tombol kembali ke Garage.
- Meta title, description, dan OG image per project.

### F8. Box, Box (Kontak)
- Judul, ajakan, quote kecil, link Email / WhatsApp / LinkedIn / GitHub (+ opsional Instagram).
- Form: nama, email, pesan. Validasi server, pesan sukses dengan animasi bendera checkered.
- Anti-spam: honeypot field + rate limit (maks. 3 kirim / 10 menit per IP).
- Pesan disimpan ke database; notifikasi email ke Dun **jika** SMTP sudah diatur (kalau gagal kirim, pesan tetap tersimpan).

### F9. Maskot pojok
- Kepala karakter di pojok kanan bawah, bubble chat berganti sesuai section yang sedang terlihat.
- Bisa ditutup (disembunyikan) oleh pengunjung.
- **Sumber data:** tabel `mascot_messages`.

### F10. Footer & 404
- Footer: garis checkered tipis, nama, teks footer, link sosmed, link menu, © tahun otomatis.
- 404: gambar `dnf.png`, judul "DNF – Did Not Find", teks, tombol "Balik ke pit".

## 7. Fitur admin (Filament, `/admin`)

### A0. Umum
- Satu akun admin (dibuat via perintah `php artisan make:filament-user`). Tidak ada registrasi publik.
- Warna utama panel: merah `#E10600`, font Inter, logo/nama "Dun #12".
- Setiap simpan perubahan konten → cache halaman depan dibersihkan otomatis.

### A1. Dashboard
- Widget: jumlah pesan belum dibaca, jumlah project terbit, jumlah pengalaman.
- Tombol cepat: "Lihat website", "Tambah project".

### A2. Pengaturan Situs (halaman tunggal)
Tab **Profil:** nama tampil, nama lengkap, peran singkat, kota, sapaan hero, tagline, deskripsi hero, teks ajakan scroll, ikon tech hero (repeater: nama + ikon).
Tab **Section:** judul & intro untuk Pit Wall, Race Progress, Garage, Box Box.
Tab **Kontak:** email, WhatsApp (format 62…), LinkedIn, GitHub, Instagram, quote kecil.
Tab **File:** upload CV (PDF, maks. 5 MB), OG image default.
Tab **SEO & Footer:** meta title, meta description, teks footer, teks 404.

### A3. Pit Wall Cards
- CRUD: label sector, judul, isi, gambar (upload), urutan (drag & drop), aktif/nonaktif.
- Maksimal 4 kartu aktif (validasi).

### A4. Experiences (Timeline)
- CRUD: nama organisasi/instansi, peran, jenis (Pendidikan / Organisasi / Kepanitiaan / Lomba / Magang / Kerja / Sertifikasi), tanggal mulai, tanggal selesai (kosong = sekarang), pencapaian, logo (opsional), urutan, aktif.
- Tabel bisa difilter per jenis.

### A5. Projects
- CRUD: judul, slug (otomatis dari judul, bisa diedit), deskripsi singkat, masalah, peran, proses (rich text), hasil, tech stack (tags), tahun, link GitHub, link demo, thumbnail, galeri (multi-upload, bisa diurutkan), unggulan (ya/tidak), status (draft/terbit), urutan.
- Tombol "Preview" membuka halaman detail di tab baru.

### A6. Mascot Messages
- Satu baris per section (hero, pit-wall, race, garage, box-box): teks bubble. Bisa diedit, tidak bisa dihapus.

### A7. Pesan Masuk
- Daftar pesan (nama, email, cuplikan, waktu, status dibaca). Hanya baca + tandai dibaca + hapus.
- Aksi "Balas via email" membuka `mailto:`.
- Badge jumlah belum dibaca di menu navigasi.

## 8. Model data
| Tabel | Kolom utama |
|---|---|
| `users` | bawaan Laravel (admin) |
| `site_settings` | key (unik), value (json) — dibaca lewat helper `setting('key')` dengan cache |
| `pit_wall_cards` | id, sector_label, title, body, image, sort, is_active |
| `experiences` | id, organization, role, type, started_at, ended_at (nullable), achievement, logo, sort, is_active |
| `projects` | id, title, slug (unik), excerpt, problem, role, process (longtext), result, tech_stack (json), year, github_url, demo_url, thumbnail, is_featured, status, sort, published_at |
| `project_images` | id, project_id, path, caption, sort |
| `mascot_messages` | id, section (unik), message |
| `contact_messages` | id, name, email, message, ip, read_at (nullable), timestamps |

Seeder awal membaca isi dari `konten.md` (diterjemahkan manual ke `DatabaseSeeder`), plus gambar pose dari `_bahan/assets`.

## 9. Kebutuhan non-fungsional
- **Performa:** Lighthouse mobile ≥ 90 (Performance, Accessibility, Best Practices, SEO). Gambar WebP, lazy load (kecuali hero), font di-preload / `font-display: swap`. Query halaman depan di-cache.
- **Responsive:** rapi dari lebar 360px sampai 1440px+. Tidak ada scroll horizontal.
- **Aksesibilitas:** kontras WCAG AA, HTML semantik, alt teks semua gambar, fokus keyboard terlihat, `prefers-reduced-motion` dihormati.
- **SEO:** `<title>`, meta description, OG & Twitter card dirender server-side (Blade), sitemap.xml (home + detail project), robots.txt, favicon dari kepala maskot.
- **Keamanan:** CSRF bawaan, validasi semua input, rate limit form & login admin, `APP_DEBUG=false` di produksi, upload dibatasi tipe (jpg/png/webp/pdf) & ukuran.
- **Ketahanan:** jika data kosong (misal belum ada project), section tetap tampil dengan pesan rapi, bukan error.

## 10. Deploy (shared hosting)
1. Lokal: `composer install --no-dev --optimize-autoloader`, `npm run build`.
2. Upload project (Git pull via SSH, atau zip) ke folder di luar `public_html`, misal `~/porto-dun`.
3. Arahkan document root domain ke `~/porto-dun/public` (atau, jika tidak bisa diubah, isi `public_html` dengan isi folder `public` dan sesuaikan path di `index.php`).
4. Buat database MySQL di panel hosting, isi `.env` (APP_ENV=production, APP_DEBUG=false, APP_URL, DB_*, MAIL_*).
5. Jalankan: `php artisan key:generate`, `php artisan migrate --force`, `php artisan db:seed --force` (sekali), `php artisan make:filament-user`, `php artisan optimize`.
6. Pastikan folder `storage`, `bootstrap/cache`, dan `public/uploads` bisa ditulis.
7. Aktifkan SSL (Let's Encrypt) dan paksa HTTPS.

## 11. Tahapan pengerjaan (milestone)
| # | Tahap | Selesai jika |
|---|---|---|
| M1 | Setup project, Tailwind, font, token warna, CLAUDE.md | Halaman kosong tampil dengan font & warna benar |
| M2 | Database, model, migration, seeder dari konten.md | `php artisan migrate --seed` jalan tanpa error |
| M3 | Admin Filament (A0–A7) | Semua konten bisa diedit & tersimpan |
| M4 | Layout + Navbar + Hero + Pit Wall | Tampil rapi di desktop & HP, data dari DB |
| M5 | Race Progress + Garage + detail project | Tampil rapi, link detail jalan |
| M6 | Box Box (form) + Footer + 404 + maskot | Form tersimpan ke admin, anti-spam aktif |
| M7 | Animasi & loading screen | Sesuai design.md bagian Motion, reduced-motion aman |
| M8 | SEO, performa, aksesibilitas, test | Lighthouse ≥ 90, feature test lulus |
| M9 | Siap deploy | Checklist bagian 10 bisa dijalankan |

## 12. Kriteria selesai (Definition of Done) per tahap
- Mengikuti `design.md` (termasuk daftar DILARANG).
- Dicek di lebar 360px dan 1280px.
- Tidak ada error di console browser maupun log Laravel.
- Ada feature test untuk: halaman home 200, detail project 200, slug salah 404, form kontak tersimpan, form honeypot ditolak, `/admin` butuh login.

## 13. Versi 2 (nanti)
- Blog & Research ("Lab") dengan halaman list + detail, dikelola di admin.
- Toggle bahasa Indonesia / English.
- Statistik pengunjung sederhana.
- Halaman "Now" (sedang mengerjakan apa).
