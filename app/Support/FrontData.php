<?php

namespace App\Support;

use App\Models\Experience;
use App\Models\MascotMessage;
use App\Models\PitWallCard;
use App\Models\Project;

/**
 * Data halaman publik dalam bentuk array sederhana, disimpan di SiteCache.
 * Cache otomatis dibersihkan saat konten diubah lewat admin.
 */
class FrontData
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function pitWall(): array
    {
        return SiteCache::remember('front.pit-wall', fn () => PitWallCard::active()
            ->limit(PitWallCard::MAX_ACTIVE)
            ->get()
            ->map(fn (PitWallCard $card) => [
                'sector' => $card->sector_label,
                'title' => $card->title,
                'body' => $card->body,
                'image' => $card->image_url,
                'srcset' => upload_srcset($card->image),
                'size' => upload_image_size($card->image),
            ])
            ->all());
    }

    /**
     * Timeline Race Progress, urut dari lap pertama.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function timeline(): array
    {
        return SiteCache::remember('front.timeline', fn () => Experience::active()
            ->get()
            ->map(fn (Experience $experience) => [
                'organization' => $experience->organization,
                'role' => $experience->role,
                'type' => $experience->type?->label(),
                'period' => $experience->period_label,
                'duration' => $experience->duration_label,
                'current' => $experience->started_at !== null && $experience->is_current,
                'achievement' => $experience->achievement,
            ])
            ->all());
    }

    /**
     * Project terbit untuk Garage, urut sesuai admin. Posisi P1, P2, … mengikuti urutan ini.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function garage(): array
    {
        return SiteCache::remember('front.garage', fn () => Project::published()
            ->get()
            ->values()
            ->map(fn (Project $project, int $i) => [
                'position' => $i + 1,
                'slug' => $project->slug,
                'title' => $project->title,
                'excerpt' => $project->excerpt,
                'tech' => $project->tech_stack ?? [],
                'year' => $project->year,
                'thumbnail' => $project->thumbnail_url,
                'srcset' => upload_srcset($project->thumbnail),
                'size' => upload_image_size($project->thumbnail),
            ])
            ->all());
    }

    /**
     * Bubble maskot per section: ['hero' => '…', 'pit-wall' => '…', …].
     *
     * @return array<string, string>
     */
    public static function mascot(): array
    {
        return SiteCache::remember('front.mascot', fn () => MascotMessage::query()
            ->whereIn('section', array_keys(MascotMessage::SECTIONS))
            ->pluck('message', 'section')
            ->filter()
            ->all());
    }
}
