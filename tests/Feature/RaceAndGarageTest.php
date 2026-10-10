<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RaceAndGarageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');
        $this->seed();
        Experience::query()->delete();
        Project::query()->get()->each->delete();
    }

    public function test_empty_timeline_and_garage_show_friendly_messages(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Belum ada stint yang tercatat.')
            ->assertSee('Belum ada project yang terbit.');
    }

    public function test_garage_lists_published_projects_as_timing_screen(): void
    {
        Project::factory()->create(['title' => 'Project Kedua', 'slug' => 'kedua', 'sort' => 2, 'tech_stack' => ['Python']]);
        Project::factory()->create(['title' => 'Project Pertama', 'slug' => 'pertama', 'sort' => 1, 'tech_stack' => ['Laravel', 'MySQL']]);
        Project::factory()->draft()->create(['title' => 'Project Draft']);

        $this->get('/')
            ->assertSeeInOrder(['P1', 'Project Pertama', 'P2', 'Project Kedua'])
            ->assertSee('Laravel')
            ->assertSee('View Lap')
            ->assertSee(route('projects.show', 'pertama'), false)
            ->assertDontSee('Project Draft');
    }
}
