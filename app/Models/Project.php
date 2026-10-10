<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use App\Models\Concerns\DeletesUploadedFiles;
use App\Models\Concerns\FlushesSiteCache;
use App\Models\Concerns\HasResponsiveImages;
use App\Models\Concerns\HasSortOrder;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'title', 'slug', 'excerpt', 'problem', 'role', 'process', 'result', 'tech_stack', 'year',
    'github_url', 'demo_url', 'thumbnail', 'is_featured', 'status', 'sort', 'published_at',
])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use DeletesUploadedFiles, FlushesSiteCache, HasFactory, HasResponsiveImages, HasSortOrder;

    protected $attributes = [
        'status' => 'draft',
    ];

    protected function casts(): array
    {
        return [
            'tech_stack' => 'array',
            'year' => 'integer',
            'is_featured' => 'boolean',
            'status' => ProjectStatus::class,
            'sort' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $project) {
            // Slug otomatis dari judul jika dikosongkan; selalu dibuat unik.
            $project->slug = static::uniqueSlug($project->slug ?: $project->title, $project->id);

            // Catat kapan pertama kali terbit.
            if ($project->status === ProjectStatus::Published && $project->published_at === null) {
                $project->published_at = now();
            }
        });

        // Hapus galeri lewat model (bukan cascade database) supaya file gambarnya ikut terhapus.
        static::deleting(fn (self $project) => $project->images->each->delete());
    }

    protected function uploadAttributes(): array
    {
        return ['thumbnail'];
    }

    /** URL detail memakai slug: /projects/{slug}. */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort')->orderBy('id');
    }

    /** Project yang tampil di Garage: berstatus terbit, urut sesuai admin. */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', ProjectStatus::Published)->orderBy('sort')->orderBy('id');
    }

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::get(fn () => upload_url($this->thumbnail));
    }

    public static function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'project';
        $slug = $base;

        for ($i = 2; static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}
