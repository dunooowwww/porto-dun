<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Garage P1–P3 (konten.md bagian 4). Semua masih placeholder; tidak ada judul, angka hasil,
 * tech stack, atau link yang dikarang. Status "terbit" supaya kerangka Garage terlihat saat
 * membangun tampilan — placeholder [ISI NANTI] sengaja terlihat jelas.
 */
class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        if (Project::exists()) {
            return;
        }

        foreach (range(1, 3) as $position) {
            Project::create([
                'title' => SiteSettingSeeder::TODO,
                'slug' => "project-p{$position}",
                'excerpt' => SiteSettingSeeder::TODO,
                'problem' => SiteSettingSeeder::TODO,
                'role' => SiteSettingSeeder::TODO,
                'process' => '<p>'.SiteSettingSeeder::TODO.'</p>',
                'result' => SiteSettingSeeder::TODO,
                'tech_stack' => [],
                'year' => null,
                'github_url' => null,
                'demo_url' => null,
                'thumbnail' => null,
                'is_featured' => true,
                'status' => ProjectStatus::Published,
                'sort' => $position,
            ]);
        }
    }
}
