<?php

namespace App\Models;

use App\Models\Concerns\DeletesUploadedFiles;
use App\Models\Concerns\FlushesSiteCache;
use App\Models\Concerns\HasResponsiveImages;
use Database\Factories\ProjectImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'path', 'caption', 'sort'])]
class ProjectImage extends Model
{
    /** @use HasFactory<ProjectImageFactory> */
    use DeletesUploadedFiles, FlushesSiteCache, HasFactory, HasResponsiveImages;

    protected function casts(): array
    {
        return [
            'sort' => 'integer',
        ];
    }

    protected function uploadAttributes(): array
    {
        return ['path'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn () => upload_url($this->path));
    }
}
