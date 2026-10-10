# Portofolio Dun #12

Website portofolio pribadi bertema F1 tipis-tipis. Dibangun dengan Laravel 13, Tailwind CSS v4, dan Alpine.js.

- Spesifikasi: [`_bahan/PRD.md`](_bahan/PRD.md)
- Aturan desain: [`_bahan/design.md`](_bahan/design.md)
- Panduan kerja & perintah: [`CLAUDE.md`](CLAUDE.md)

## Menjalankan di lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
composer run dev
```

Buka http://127.0.0.1:8000
