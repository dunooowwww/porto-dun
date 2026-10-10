<?php

namespace Database\Seeders;

use App\Models\MascotMessage;
use Illuminate\Database\Seeder;

/**
 * Bubble chat maskot per section (konten.md bagian 7).
 */
class MascotMessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            'hero' => 'Halo! Siap balapan?',
            'pit-wall' => 'Ini yang bikin aku jalan.',
            'race' => 'Masih banyak lap di depan!',
            'garage' => 'Cek mesin-mesin buatanku!',
            'box-box' => 'Pit stop dulu, yuk ngobrol!',
        ];

        foreach ($messages as $section => $message) {
            MascotMessage::firstOrCreate(['section' => $section], ['message' => $message]);
        }
    }
}
