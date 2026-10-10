<?php

namespace Database\Seeders;

use App\Enums\ExperienceCategory;
use App\Enums\ExperienceType;
use App\Enums\ProjectStatus;
use App\Models\Experience;
use App\Models\PitWallCard;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Data asli dari CV & portofolio Ardan (Oktober 2026), supaya di hosting tidak perlu input ulang.
 * File pendukung (CV PDF + screenshot project) ada di database/seeders/files/.
 *
 * Aman dijalankan ulang dan tidak menimpa editan admin:
 * - Pengaturan hanya diisi kalau masih kosong, "[ISI NANTI]", atau masih nilai bawaan SiteSettingSeeder.
 * - Pengalaman/project placeholder "[ISI NANTI]" dihapus; data asli dibuat kalau belum ada
 *   (dicocokkan lewat organisasi + peran / slug), yang sudah ada tidak diubah.
 * - Teks kartu Pit Wall hanya diisi kalau masih "[ISI NANTI]".
 */
class CvContentSeeder extends Seeder
{
    public const FILES = __DIR__.'/files';

    public function run(): void
    {
        $this->seedSettings();
        $this->seedPitWall();
        $this->seedExperiences();
        $this->seedProjects();
    }

    protected function seedSettings(): void
    {
        $defaults = (new SiteSettingSeeder)->settings();
        $current = SiteSetting::allCached();

        foreach ($this->settings() as $key => $value) {
            $old = $current[$key] ?? null;

            if (blank($old) || $old === SiteSettingSeeder::TODO || $old === ($defaults[$key] ?? null)) {
                SiteSetting::set($key, value($value));
            }
        }
    }

    /**
     * @return array<string, mixed> nilai atau Closure yang menghasilkan nilai
     */
    public function settings(): array
    {
        return [
            'full_name' => 'Ardan Haryanto Putra',
            'city' => 'Yogyakarta, Indonesia',
            'hero_tech_icons' => [
                ['name' => 'Python', 'icon' => 'code'],
                ['name' => 'SQL / MySQL', 'icon' => 'database'],
                ['name' => 'Machine Learning', 'icon' => 'brain'],
                ['name' => 'Power BI', 'icon' => 'chart-bar'],
                ['name' => 'BPMN / Camunda', 'icon' => 'sitemap'],
                ['name' => 'QGIS', 'icon' => 'map'],
            ],
            'contact_email' => 'ardanputra405@gmail.com',
            'contact_whatsapp' => '6281391381661',
            'contact_linkedin' => 'https://www.linkedin.com/in/ardanptrr12/',
            'contact_github' => 'https://github.com/Bmaxx12',
            // Closure: file CV baru disalin kalau pengaturannya memang akan diisi.
            'cv_path' => fn () => $this->copyFile('cv-ardan-haryanto-putra.pdf', 'files/cv-ardan-haryanto-putra.pdf'),
            'meta_description' => 'Portofolio Ardan Haryanto Putra (Dun), mahasiswa Sistem Informasi UPN "Veteran" Yogyakarta: system analyst, data, dan AI.',
        ];
    }

    protected function seedPitWall(): void
    {
        $bodies = [
            'AI/ML Enthusiast' => 'Magang Machine Learning Engineer di Widya Analytic dan ikut cohort AI Engineer Coding Camp DBS Foundation × Dicoding: analisis sentimen, benchmark LLM, sampai chatbot RAG.',
            'Builder Mindset' => 'Pegang project dari analisis kebutuhan sampai deploy: sistem informasi magang PLN (Laravel + MySQL), website travel, sampai aplikasi cuaca Flutter.',
            'Team Player' => 'Wakil Kepala Medkominfo HMSI dan ketua pelaksana dua event besar: 100+ panitia, 1.700+ peserta.',
            'Business & Strategy' => 'Juara 3 Business Model Canvas Competition, Co-Head Business Development di Candidate College, terbiasa memetakan proses dengan BPMN/Camunda.',
        ];

        foreach ($bodies as $title => $body) {
            PitWallCard::query()
                ->where('title', $title)
                ->where('body', SiteSettingSeeder::TODO)
                ->get()
                ->each->update(['body' => $body]);
        }
    }

    protected function seedExperiences(): void
    {
        Experience::query()->where('organization', SiteSettingSeeder::TODO)->get()->each->delete();

        foreach ($this->experiences() as $i => $experience) {
            Experience::firstOrCreate(
                ['organization' => $experience['organization'], 'role' => $experience['role']],
                $experience + ['sort' => $i + 1, 'is_active' => true],
            );
        }
    }

    /**
     * Urutan sama dengan CV. Tanggal mulai/selesai memakai tanggal 1 bulan tersebut; ended_at null = sekarang.
     *
     * @return array<int, array<string, mixed>>
     */
    public function experiences(): array
    {
        return [
            // Professional experience
            [
                'organization' => 'PT Widya Intelektual Bangsa (Widya Analytic)',
                'role' => 'Machine Learning Engineer Intern',
                'type' => ExperienceType::Magang,
                'category' => ExperienceCategory::Professional,
                'started_at' => '2026-08-01',
                'ended_at' => null,
                'achievement' => 'Scraping ulasan Google Play untuk analisis sentimen, benchmark beberapa LLM, dan membangun chatbot RAG yang terhubung ke Odoo.',
            ],
            [
                'organization' => 'Coding Camp powered by DBS Foundation × Dicoding',
                'role' => 'AI Engineer Cohort',
                'type' => ExperienceType::Magang,
                'category' => ExperienceCategory::Professional,
                'started_at' => '2026-02-01',
                'ended_at' => '2026-07-01',
                'achievement' => 'Menyusun kebutuhan & cakupan fitur SkillMap-AI dan menjalankan persiapan data end-to-end (cleaning, preprocessing, feature engineering).',
            ],
            [
                'organization' => 'PT PLN (Persero)',
                'role' => 'IT Intern',
                'type' => ExperienceType::Magang,
                'category' => ExperienceCategory::Professional,
                'started_at' => '2025-12-01',
                'ended_at' => '2026-02-01',
                'achievement' => 'Memegang sistem informasi magang berbasis web dari analisis kebutuhan sampai deploy, dan memetakan 1.000+ aset perusahaan dengan SAP & QGIS.',
            ],
            [
                'organization' => 'Candidate College',
                'role' => 'Co-Head of Business Development',
                'type' => ExperienceType::Kerja,
                'category' => ExperienceCategory::Professional,
                'started_at' => '2025-08-01',
                'ended_at' => '2025-12-01',
                'achievement' => 'Memimpin tim 5 orang untuk riset tren produk & perilaku konsumen, serta menyelaraskan prioritas antar divisi.',
            ],
            [
                'organization' => 'Kimia Farma × Rakamin Academy',
                'role' => 'Big Data Analyst Intern',
                'type' => ExperienceType::Magang,
                'category' => ExperienceCategory::Professional,
                'started_at' => '2024-01-01',
                'ended_at' => '2024-02-01',
                'achievement' => 'Mengolah data penjualan dengan MySQL dan analisis statistik, lalu menyajikannya lewat dashboard Looker Studio.',
            ],

            // Organizational & leadership experience
            [
                'organization' => 'Himpunan Mahasiswa Sistem Informasi (HMSI)',
                'role' => 'Wakil Kepala Departemen Media, Komunikasi & Informasi',
                'type' => ExperienceType::Organisasi,
                'category' => ExperienceCategory::Organizational,
                'started_at' => '2025-03-01',
                'ended_at' => '2026-04-01',
                'achievement' => 'Menyusun strategi konten dan mengelola tim 10 orang, termasuk anggaran, jadwal, dan program kerja departemen.',
            ],
            [
                'organization' => 'Blora Education Fair 2025 & FOKS ORCA 2025',
                'role' => 'Ketua Pelaksana',
                'type' => ExperienceType::Kepanitiaan,
                'category' => ExperienceCategory::Organizational,
                'started_at' => '2024-08-01',
                'ended_at' => '2025-01-01',
                'achievement' => 'Memimpin 7–8 divisi dan 100+ panitia di dua event besar (1.700+ peserta), termasuk kemitraan dengan pihak luar.',
            ],
            [
                'organization' => 'PKK FTI (Orientasi Mahasiswa Baru Fakultas Teknik)',
                'role' => 'Wakil Koordinator Acara',
                'type' => ExperienceType::Kepanitiaan,
                'category' => ExperienceCategory::Organizational,
                'started_at' => '2025-06-01',
                'ended_at' => '2025-08-01',
                'achievement' => 'Merancang konsep acara dan mengoordinasi tim 7 orang.',
            ],

            // Pendidikan, lomba, sertifikasi: tidak tampil di chart Race Strategy (kategori kosong).
            [
                'organization' => 'UPN "Veteran" Yogyakarta',
                'role' => 'S1 Sistem Informasi',
                'type' => ExperienceType::Pendidikan,
                'category' => null,
                'started_at' => '2023-08-01',
                'ended_at' => null,
                'achievement' => 'IPK 3,84 / 4,00.',
            ],
            [
                'organization' => 'FKBMIK, UPN "Veteran" Jawa Timur',
                'role' => 'Juara 3 Business Model Canvas Competition',
                'type' => ExperienceType::Lomba,
                'category' => null,
                'started_at' => null,
                'ended_at' => null,
                'achievement' => 'Tahun 2025.',
            ],
            [
                'organization' => 'Universitas Pendidikan Indonesia (UPI)',
                'role' => 'Finalis Logistic National Competition',
                'type' => ExperienceType::Lomba,
                'category' => null,
                'started_at' => null,
                'ended_at' => null,
                'achievement' => 'Tahun 2026.',
            ],
            [
                'organization' => 'BNSP',
                'role' => 'Sertifikasi Junior Web Developer',
                'type' => ExperienceType::Sertifikasi,
                'category' => null,
                'started_at' => null,
                'ended_at' => null,
                'achievement' => 'Tahun 2025.',
            ],
        ];
    }

    protected function seedProjects(): void
    {
        Project::query()->where('title', SiteSettingSeeder::TODO)->get()->each->delete();

        foreach ($this->projects() as $i => $data) {
            if (Project::where('slug', $data['slug'])->exists()) {
                continue;
            }

            $images = $data['images'];
            unset($data['images']);

            $project = Project::create($data + [
                'thumbnail' => $this->copyFile("projects/{$data['slug']}-1.webp", "projects/{$data['slug']}.webp"),
                'demo_url' => null,
                'is_featured' => true,
                'status' => ProjectStatus::Published,
                'sort' => $i + 1,
            ]);

            foreach ($images as $j => $caption) {
                $number = $j + 2;

                $project->images()->create([
                    'path' => $this->copyFile("projects/{$data['slug']}-{$number}.webp", "projects/gallery/{$data['slug']}-{$number}.webp"),
                    'caption' => $caption,
                    'sort' => $j + 1,
                ]);
            }
        }
    }

    /**
     * Urutan sama dengan PDF portofolio. "images" = caption galeri; file-nya
     * files/projects/{slug}-2.webp, -3.webp, … (file -1 dipakai sebagai thumbnail).
     *
     * @return array<int, array<string, mixed>>
     */
    public function projects(): array
    {
        return [
            [
                'slug' => 'pln-internship',
                'title' => 'PLN Internship Information System',
                'excerpt' => 'Sistem informasi magang berbasis web untuk PLN UP3 Semarang: pendaftaran, penempatan divisi, monitoring progres, dan dokumen dalam satu tempat.',
                'problem' => 'Administrasi peserta magang di PLN UP3 Semarang perlu dibuat lebih rapi dan efisien: pendaftaran, penempatan divisi, pemantauan progres, sampai pengelolaan dokumen.',
                'role' => 'System Analyst & Full Stack Web Developer',
                'process' => '<p>Menganalisis proses bisnis, mengumpulkan kebutuhan sistem, dan merancang alur sistem, lalu mengembangkan aplikasinya dengan Laravel dan MySQL.</p>',
                'result' => 'Pendaftaran peserta, penempatan divisi, monitoring progres, dan manajemen dokumen terpusat di satu platform, sehingga administrasi magang jadi lebih efisien, akurat, dan transparan.',
                'tech_stack' => ['Laravel', 'MySQL'],
                'year' => 2026,
                'github_url' => 'https://github.com/Bmaxx12/Project-Web-PLN',
                'images' => ['Halaman login', 'Dashboard admin'],
            ],
            [
                'slug' => 'satriatama',
                'title' => 'Satriatama Tour Travel Website',
                'excerpt' => 'Website pemesanan perjalanan untuk Satriatama Tour, dari SRS dan prototipe sampai UAT.',
                'problem' => 'Satriatama Tour butuh layanan digital untuk pemesanan perjalanan supaya layanan ke pelanggan dan operasional lebih efisien.',
                'role' => 'System Analyst & Full Stack Web Developer',
                'process' => '<p>Mengumpulkan kebutuhan bisnis, menyusun Software Requirements Specification (SRS), merancang prototipe, lalu mengembangkan aplikasinya dengan PHP dan MySQL.</p><p>Setelah itu menjalankan system testing dan User Acceptance Testing (UAT).</p>',
                'result' => 'Website pemesanan perjalanan yang sudah diuji lewat system testing dan UAT, sesuai kebutuhan bisnis.',
                'tech_stack' => ['PHP', 'MySQL'],
                'year' => 2025,
                'github_url' => 'https://github.com/Bmaxx12/Satriatama-Tour.github.io',
                'images' => ['Prototipe tampilan', 'Data Flow Diagram level 1'],
            ],
            [
                'slug' => 'pantauin',
                'title' => 'Pantauin Web',
                'excerpt' => 'Platform analitik keuangan berbasis AI untuk UMKM Indonesia, dibuat untuk Digdaya X Hackathon 2026.',
                'problem' => 'Banyak UMKM di Indonesia belum punya gambaran kesehatan keuangan dan kesiapan kredit yang jelas, sehingga sulit mengakses pembiayaan formal.',
                'role' => 'System Analyst & AI Solution Designer',
                'process' => '<p>Menganalisis kebutuhan bisnis, merancang arsitektur sistem, dan mendefinisikan alur AI untuk credit scoring serta analisis risiko keuangan.</p>',
                'result' => 'Platform yang mengubah data transaksi menjadi insight, membantu pemilik UMKM mengambil keputusan keuangan dan membuka akses ke pembiayaan formal.',
                'tech_stack' => ['AI', 'Credit Scoring'],
                'year' => 2026,
                'github_url' => 'https://github.com/dzaktama/pantauin',
                'images' => ['Dashboard bisnis', 'Simulasi stress test bisnis'],
            ],
            [
                'slug' => 'skillmap-ai',
                'title' => 'SkillMap-AI',
                'excerpt' => 'Platform rekomendasi karier berbasis AI: membaca CV, menghitung career readiness score, dan menunjukkan skill gap. Capstone Coding Camp DBS × Dicoding.',
                'problem' => 'Pencari kerja sering tidak tahu skill apa yang masih kurang untuk karier yang dituju.',
                'role' => 'AI Engineer',
                'process' => '<p>Menganalisis kebutuhan pengguna, merancang alur AI, dan mengembangkan solusi berbasis NLP untuk mengekstrak serta menilai informasi dari CV pengguna.</p>',
                'result' => 'Career readiness score dan rekomendasi karier yang personal, sehingga pengguna bisa melihat skill gap dan merencanakan pengembangan kariernya.',
                'tech_stack' => ['NLP'],
                'year' => 2026,
                'github_url' => 'https://github.com/haikalmol/SkillMap-AI',
                'images' => ['Career Readiness Analyzer', 'Progress tracker', 'Career Quest'],
            ],
            [
                'slug' => 'kimia-farma-dashboard',
                'title' => 'Kimia Farma Sales Analytics Dashboard',
                'excerpt' => 'Dashboard penjualan interaktif di Looker Studio dari virtual internship Big Data Analytics Kimia Farma × Rakamin.',
                'problem' => 'Stakeholder butuh cara cepat memantau performa penjualan untuk mengambil keputusan berbasis data.',
                'role' => 'Big Data Analyst',
                'process' => '<p>Mengumpulkan, membersihkan, dan mentransformasi data penjualan dengan MySQL, lalu merancang dashboard interaktif di Looker Studio.</p><p><a href="https://www.linkedin.com/in/ardanptrr12/details/experience/">Detail pengalaman di LinkedIn</a></p>',
                'result' => 'Dashboard yang menampilkan performa penjualan, aktivitas pelanggan, tren pendapatan, dan performa cabang, sehingga peluang bisnis lebih mudah terlihat.',
                'tech_stack' => ['MySQL', 'Looker Studio'],
                'year' => 2024,
                'github_url' => null,
                'images' => [],
            ],
            [
                'slug' => 'weather-app',
                'title' => 'Forecasting Weather App',
                'excerpt' => 'Aplikasi prakiraan cuaca Flutter dengan Pro Plan dan rekomendasi dari Gemini API. Final project mata kuliah Pengembangan Aplikasi Mobile.',
                'problem' => 'Final project mata kuliah Pengembangan Aplikasi Mobile: membuat aplikasi prakiraan cuaca real-time yang nyaman dipakai.',
                'role' => 'Flutter Developer',
                'process' => '<p>Membangun aplikasi dengan Flutter: prakiraan cuaca real-time dan antarmuka yang intuitif.</p><p>Menambahkan sistem upgrade Pro Plan untuk fitur premium, serta integrasi Gemini API untuk rekomendasi berdasarkan kondisi cuaca.</p>',
                'result' => 'Aplikasi mobile dengan prakiraan real-time, fitur premium Pro Plan, dan rekomendasi pintar dari Gemini API.',
                'tech_stack' => ['Flutter', 'Gemini API'],
                'year' => 2025,
                'github_url' => 'https://github.com/Bmaxx12/Mobile-Weather-Forecasting-App',
                'images' => ['Prakiraan cuaca hari ini', 'Pilihan paket Basic & Pro', 'Konfirmasi langganan Pro', 'Halaman login'],
            ],
            [
                'slug' => 'graphie',
                'title' => 'Graphie: Graph RAG Chatbot for Odoo 19',
                'excerpt' => 'Addon Odoo 19 yang memasang Graph RAG di ERP, supaya pengguna bisa bertanya soal data bisnisnya dengan bahasa sehari-hari.',
                'problem' => 'Pengguna ERP perlu cara yang lebih mudah untuk mencari informasi dari data dan dokumen bisnis di Odoo, tanpa harus menelusuri menu satu per satu.',
                'role' => 'AI Engineer Intern (Widya Analytic)',
                'process' => '<p>Membuat addon custom Odoo 19 bernama Graphie. LightRAG berjalan di Docker berdampingan dengan Odoo 19 lokal untuk mengekstrak entitas dan relasi dari record serta dokumen Odoo menjadi knowledge graph.</p><p>Knowledge graph digabung dengan vector search untuk mengambil konteks. SumoPod dipakai sebagai penyedia LLM dan embedding.</p>',
                'result' => 'Chatbot di dalam Odoo yang menjawab pertanyaan berdasarkan dokumen dan data perusahaan.',
                'tech_stack' => ['Odoo 19', 'LightRAG', 'Docker', 'SumoPod'],
                'year' => 2026,
                'github_url' => 'https://github.com/Bmaxx12/odoo-graphie',
                'images' => ['Jawaban chatbot Graphie', 'Halaman addon di Odoo'],
            ],
        ];
    }

    /**
     * Salin database/seeders/files/{source} ke disk "uploads" (kalau belum ada) dan kembalikan path-nya.
     */
    protected function copyFile(string $source, string $target): ?string
    {
        $path = self::FILES.'/'.$source;

        if (! File::exists($path)) {
            return null;
        }

        if (! Storage::disk('uploads')->exists($target)) {
            Storage::disk('uploads')->put($target, File::get($path));
        }

        return $target;
    }
}
