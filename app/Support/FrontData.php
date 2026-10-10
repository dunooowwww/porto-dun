<?php

namespace App\Support;

use App\Enums\ExperienceCategory;
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
     * Chart Race Strategy: satu tab per kategori yang punya data (Profesional dulu, lalu Organisasi).
     * Hanya pengalaman aktif yang punya kategori & tanggal mulai; urut dari yang paling awal.
     * Posisi bar dihitung dalam persen dari rentang tahun tab itu sendiri.
     * Kunci cache memakai bulan berjalan supaya bar yang masih berjalan ikut memanjang.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function raceStrategy(): array
    {
        return SiteCache::remember('front.race.'.now()->format('Y-m'), function () {
            $now = now();
            $tabs = [];

            foreach (ExperienceCategory::cases() as $category) {
                $items = Experience::active()
                    ->where('category', $category)
                    ->whereNotNull('started_at')
                    ->get()
                    ->sortBy('started_at')
                    ->values();

                if ($items->isEmpty()) {
                    continue;
                }

                $firstYear = $items->min(fn (Experience $e) => $e->started_at->year);
                $lastYear = $items->max(fn (Experience $e) => ($e->ended_at ?? $now)->year);
                $rangeStart = $firstYear * 12;
                $totalMonths = ($lastYear - $firstYear + 1) * 12;

                $stints = $items->map(function (Experience $e, int $i) use ($now, $rangeStart, $totalMonths) {
                    $end = $e->ended_at ?? $now;
                    $startMonth = $e->started_at->year * 12 + $e->started_at->month - 1;
                    $endMonth = $end->year * 12 + $end->month; // eksklusif: bulan terakhir ikut dihitung

                    return [
                        'number' => $i + 1,
                        'dialog' => 'stint-'.$e->id,
                        'organization' => $e->organization,
                        'role' => $e->role,
                        'type' => $e->type?->label(),
                        'period' => $e->period_label,
                        'duration' => $e->duration_label,
                        'current' => $e->is_current,
                        'achievement' => $e->achievement,
                        'logo' => $e->logo_url,
                        'left' => round(($startMonth - $rangeStart) / $totalMonths * 100, 3),
                        'width' => round(max(1, $endMonth - $startMonth) / $totalMonths * 100, 3),
                    ];
                })->all();

                $tabs[] = [
                    'key' => $category->value,
                    'race' => $category->raceLabel(),
                    'label' => $category->label(),
                    'years' => range($firstYear, $lastYear),
                    'stints' => $stints,
                ];
            }

            return $tabs;
        });
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
