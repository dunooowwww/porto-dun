<?php

namespace Database\Seeders;

use App\Models\PitWallCard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * 4 kartu Pit Wall (konten.md bagian 2). Gambar pose disalin dari public/images ke disk "uploads",
 * supaya nanti bisa diganti lewat admin seperti upload biasa.
 */
class PitWallCardSeeder extends Seeder
{
    public function run(): void
    {
        if (PitWallCard::exists()) {
            return;
        }

        $cards = [
            ['SECTOR 1', 'AI/ML Enthusiast', 'ai-ml'],
            ['SECTOR 2', 'Builder Mindset', 'builder'],
            ['SECTOR 3', 'Team Player', 'team'],
            ['SECTOR 4', 'Business & Strategy', 'strategy'],
        ];

        foreach ($cards as $i => [$sector, $title, $image]) {
            PitWallCard::create([
                'sector_label' => $sector,
                'title' => $title,
                'body' => SiteSettingSeeder::TODO,
                'image' => self::copyImage($image, 'pit-wall'),
                'sort' => $i + 1,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Salin public/images/{name}.webp ke public/uploads/{folder}/{name}.webp dan kembalikan path-nya.
     */
    public static function copyImage(string $name, string $folder): ?string
    {
        $source = public_path("images/{$name}.webp");
        $target = "{$folder}/{$name}.webp";

        if (! File::exists($source)) {
            return null;
        }

        if (! Storage::disk('uploads')->exists($target)) {
            Storage::disk('uploads')->put($target, File::get($source));
        }

        return $target;
    }
}
