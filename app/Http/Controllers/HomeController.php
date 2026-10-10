<?php

namespace App\Http\Controllers;

use App\Models\PitWallCard;
use App\Support\SiteCache;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Halaman depan one-page. Data dari database disimpan di SiteCache dan otomatis
     * dibersihkan saat konten diubah lewat admin.
     */
    public function __invoke(): View
    {
        $cards = SiteCache::remember('home.pit-wall', fn () => PitWallCard::active()
            ->limit(PitWallCard::MAX_ACTIVE)
            ->get()
            ->map(fn (PitWallCard $card) => [
                'sector' => $card->sector_label,
                'title' => $card->title,
                'body' => $card->body,
                'image' => $card->image_url,
                'size' => static::imageSize($card->image),
            ])
            ->all());

        return view('home', [
            'cards' => $cards,
        ]);
    }

    /**
     * Ukuran asli gambar upload untuk atribut width/height (mencegah layout bergeser).
     *
     * @return array{0: int, 1: int}|null
     */
    protected static function imageSize(?string $path): ?array
    {
        if (blank($path) || ! Storage::disk('uploads')->exists($path)) {
            return null;
        }

        $size = @getimagesize(Storage::disk('uploads')->path($path));

        return $size ? [$size[0], $size[1]] : null;
    }
}
