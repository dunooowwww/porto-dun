# Design Guide – Portofolio Dun #12

Baca file ini SEBELUM menulis UI apa pun, dan patuhi di setiap section.
Kalau ragu antara "aman tapi generik" vs "sesuai guide ini", pilih guide ini.

---

## 1. Konsep
- Struktur & kebersihan ala portoanam.com: one-page, minimalis, banyak whitespace, bento grid, maskot karakter.
- Tema: F1 tipis-tipis. **80% minimalis, 20% racing.** Racing muncul di aksen, label, copywriting, dan motion — bukan di background atau dekorasi besar.
- Kepribadian: santai, percaya diri, sedikit jahil. Rapi seperti pit crew, cepat seperti start balapan.
- Identitas: nomor balap **#12**. Maskot = karakter anime hoodie putih, topi hitam #12 (gambar di public/images).

## 2. Warna (hanya ini, jangan tambah warna lain)
| Token | Hex | Pakai untuk |
|---|---|---|
| bg | #F7F7F5 | Background halaman |
| card | #FFFFFF | Kartu, form, navbar |
| ink | #111111 | Heading, teks utama, tombol utama |
| muted | #6B6B6B | Teks sekunder |
| line | #E6E6E3 | Border tipis 1px |
| red | #E10600 | Aksen: garis bawah nama, menu aktif, #12, checkpoint, lampu start |
| red-dark | #B80500 | Teks kecil berwarna merah, hover |

Aturan merah: maksimal ~5% area layar. Merah = sinyal, bukan dekorasi.
Tombol utama HITAM dengan ikon/detail kecil merah — bukan tombol merah penuh.

## 3. Tipografi
- Heading: **Archivo** (700–900), tracking rapat (-0.02em s/d -0.04em). Boleh italic untuk kata yang ingin "melesat".
- Body: **Inter** 400–500, ukuran 16–18px, line-height 1.6.
- Label / angka / telemetri: **JetBrains Mono**, uppercase, kecil (11–12px), letter-spacing lebar (0.08em). Contoh: `SECTOR 1`, `P1`, `LAP 03`, `#12`.
- Skala jelas & kontras: H1 hero sangat besar (clamp 48px–96px), lompatan ukuran tegas antar level.

## 4. Layout & spacing
- Container max ±1200px, gutter 16px di HP, 24–32px di desktop.
- Spacing kelipatan 4/8px. Jarak antar section lega (96–160px di desktop).
- Radius konsisten: kartu 24px, tombol/pill 999px, elemen kecil 12px.
- Bento grid Pit Wall: ukuran kartu TIDAK seragam (misal 2/3 + 1/3, lalu 1/3 + 2/3), seperti portoanam.
- Hero: teks rata kiri, gambar karakter di kanan. Jangan centered-hero generik.
- Border 1px warna `line` + shadow sangat halus (atau tanpa shadow). Kartu terasa "terangkat" karena putih di atas off-white.

## 5. Elemen F1 (pakai hanya ini, takarannya tipis)
- Loading screen: 5 lampu merah menyala satu per satu lalu padam (±1,5 detik total, bisa di-skip, hanya sekali per sesi).
- Label telemetri mono di pojok kartu (`SECTOR 1–4`, `P1–P3`).
- Race Progress: garis lintasan horizontal tipis, checkpoint bulat merah, mobil kecil bergerak saat scroll.
- Garage: kartu project gaya timing screen (posisi P1/P2/P3, nama, stack, "View Lap →").
- Garis checkered tipis (4–6px) hanya di atas footer.
- Copywriting: "Lights out", "Box, box", "DNF – Did Not Find". Secukupnya, jangan setiap kalimat.

## 6. Motion
- Karakter: cepat & tegas (ease-out, 200–400ms), masuk sedikit dari kiri seperti "melesat". Bukan fade pelan yang mengambang.
- Stagger kecil (40–80ms) antar item dalam satu section.
- Hover: kartu naik 2–4px, border jadi lebih gelap. Link: garis bawah merah tumbuh dari kiri.
- Maskot pojok: muncul dengan bubble chat yang berganti per section (pakai IntersectionObserver).
- Wajib hormati `prefers-reduced-motion`: matikan animasi besar & loading screen.

## 7. DILARANG (anti AI slop)
- Gradient ungu/biru/pink, gradient text, glow neon, glassmorphism, blob blur di background.
- Emoji sebagai ikon. Gunakan ikon garis tipis konsisten (Lucide / Tabler), stroke 1.5.
- Kartu seragam 3 kolom dengan ikon di atas + judul + paragraf (template SaaS).
- Kalimat generik: "Passionate developer", "Crafting digital experiences", "Let's build something amazing", "Lorem ipsum". Pakai teks dari konten.md.
- Shadow tebal, border radius campur aduk, lebih dari 3 font.
- Statistik palsu / angka karangan. Kalau data belum ada, tulis placeholder jelas: `[ISI NANTI]`.
- Logo merek asli (F1, tim, sponsor, Apple, dll.) dan font resmi F1.
- Animasi di semua elemen sekaligus, parallax berlebihan, cursor custom.
- Merah dipakai untuk area besar (background section, tombol penuh di mana-mana).

## 8. Kualitas wajib
- Responsive mulai 360px. Cek tiap section di HP sebelum lanjut animasi. Tanpa scroll horizontal.
- Kontras teks memenuhi WCAG AA. Teks kecil merah pakai `red-dark`.
- Semua gambar: `alt` jelas, `loading="lazy"` (kecuali hero), format WebP kalau bisa.
- HTML semantik (header, nav, main, section, footer), heading berurutan, fokus keyboard terlihat (outline merah).
- `<title>` dan meta description sudah benar di HTML server (Blade), bukan diisi lewat JavaScript. Tambahkan OG image.
- Satu sumber token: definisikan warna, font, radius di config Tailwind / CSS variables; jangan hardcode hex di komponen.

## 9. Admin panel (Filament)
- Pakai tampilan bawaan Filament; jangan di-custom berlebihan.
- Cukup ubah: warna utama #E10600, font Inter, nama brand "Dun #12", favicon maskot.
- Label, helper text, dan pesan validasi dalam Bahasa Indonesia, jelas untuk non-programmer.
- Aturan "DILARANG" di bagian 7 berlaku untuk halaman publik; admin cukup rapi dan fungsional.

## 10. Cara kerja
- Sebelum coding tiap section, tulis rencana singkat layout-nya dulu (2–4 poin).
- Setelah section selesai, cek ulang terhadap bagian 7 (DILARANG) dan 8 (Kualitas).
- Kalau ada keputusan desain yang tidak diatur di sini, pilih yang paling sederhana dan konsisten dengan portoanam.com, lalu sebutkan pilihannya.
