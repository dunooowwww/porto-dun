<?php

namespace App\Console\Commands;

use App\Models\Experience;
use App\Models\PitWallCard;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Support\ImageVariants;
use App\Support\SiteCache;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('images:variants')]
#[Description('Buat ulang varian ukuran (srcset) untuk semua gambar upload yang sudah ada')]
class GenerateImageVariants extends Command
{
    public function handle(): int
    {
        $paths = collect()
            ->merge(PitWallCard::pluck('image'))
            ->merge(Experience::pluck('logo'))
            ->merge(Project::pluck('thumbnail'))
            ->merge(ProjectImage::pluck('path'))
            ->filter()
            ->unique();

        $this->withProgressBar($paths, fn (string $path) => ImageVariants::generate($path));
        SiteCache::flush();

        $this->newLine();
        $this->info("Selesai: {$paths->count()} gambar diproses.");

        return self::SUCCESS;
    }
}
