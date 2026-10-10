<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

/**
 * Timeline Race Progress (konten.md bagian 3). Semua lap masih placeholder: nama, peran, jenis,
 * dan tanggal tidak dikarang. Isi lewat admin; durasi akan dihitung otomatis dari tanggal.
 */
class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        if (Experience::exists()) {
            return;
        }

        foreach (range(1, 3) as $lap) {
            Experience::create([
                'organization' => SiteSettingSeeder::TODO,
                'role' => SiteSettingSeeder::TODO,
                'type' => null,
                'started_at' => null,
                'ended_at' => null,
                'achievement' => null,
                'sort' => $lap,
                'is_active' => true,
            ]);
        }
    }
}
