<?php

namespace App\Models;

use App\Models\Concerns\DeletesUploadedFiles;
use App\Models\Concerns\FlushesSiteCache;
use App\Models\Concerns\HasSortOrder;
use Database\Factories\PitWallCardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['sector_label', 'title', 'body', 'image', 'sort', 'is_active'])]
class PitWallCard extends Model
{
    /** @use HasFactory<PitWallCardFactory> */
    use DeletesUploadedFiles, FlushesSiteCache, HasFactory, HasSortOrder;

    /** Bento grid Pit Wall maksimal 4 kartu aktif (PRD A3). */
    public const MAX_ACTIVE = 4;

    protected function casts(): array
    {
        return [
            'sort' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected function uploadAttributes(): array
    {
        return ['image'];
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => upload_url($this->image));
    }
}
