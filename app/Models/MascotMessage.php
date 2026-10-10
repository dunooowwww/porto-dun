<?php

namespace App\Models;

use App\Models\Concerns\FlushesSiteCache;
use Database\Factories\MascotMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Satu bubble chat maskot per section. Bisa diedit, tidak bisa dihapus (PRD A6).
 */
#[Fillable(['section', 'message'])]
class MascotMessage extends Model
{
    /** @use HasFactory<MascotMessageFactory> */
    use FlushesSiteCache, HasFactory;

    /** Kunci section (sama dengan id section di halaman) → nama tampil. */
    public const SECTIONS = [
        'hero' => 'Hero',
        'pit-wall' => 'Pit Wall',
        'race' => 'Race Progress',
        'garage' => 'Garage',
        'box-box' => 'Box, Box',
    ];
}
