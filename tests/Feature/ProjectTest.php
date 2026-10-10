<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_is_generated_from_title_and_kept_unique(): void
    {
        $first = Project::factory()->create(['title' => 'Sistem Antrian Klinik', 'slug' => null]);
        $second = Project::factory()->create(['title' => 'Sistem Antrian Klinik', 'slug' => null]);

        $this->assertSame('sistem-antrian-klinik', $first->slug);
        $this->assertSame('sistem-antrian-klinik-2', $second->slug);
        $this->assertSame('slug', $first->getRouteKeyName());
    }

    public function test_saving_again_keeps_the_same_slug(): void
    {
        $project = Project::factory()->create(['title' => 'Dashboard', 'slug' => null]);
        $project->update(['excerpt' => 'Diubah']);

        $this->assertSame('dashboard', $project->fresh()->slug);
    }

    public function test_published_scope_hides_drafts_and_follows_sort(): void
    {
        Project::factory()->create(['title' => 'Kedua', 'sort' => 2]);
        Project::factory()->create(['title' => 'Pertama', 'sort' => 1]);
        Project::factory()->draft()->create(['title' => 'Draft']);

        $this->assertSame(['Pertama', 'Kedua'], Project::published()->pluck('title')->all());
    }

    public function test_published_at_is_filled_when_published(): void
    {
        $draft = Project::factory()->draft()->create();
        $this->assertNull($draft->published_at);

        $draft->update(['status' => ProjectStatus::Published]);
        $this->assertNotNull($draft->fresh()->published_at);
    }

    public function test_images_are_ordered_and_deleted_with_project(): void
    {
        $project = Project::factory()->create();
        ProjectImage::factory()->for($project)->create(['sort' => 2, 'path' => 'projects/b.webp']);
        ProjectImage::factory()->for($project)->create(['sort' => 1, 'path' => 'projects/a.webp']);

        $this->assertSame(['projects/a.webp', 'projects/b.webp'], $project->images->pluck('path')->all());
        $this->assertStringEndsWith('/uploads/projects/a.webp', $project->images->first()->url);
        $this->assertSame(['Laravel', 'PHP'], Project::factory()->create(['tech_stack' => ['Laravel', 'PHP']])->tech_stack);

        $project->delete();
        $this->assertSame(0, ProjectImage::count());
    }
}
