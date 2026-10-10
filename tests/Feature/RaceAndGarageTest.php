<?php

namespace Tests\Feature;

use App\Enums\ExperienceType;
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

    public function test_race_progress_shows_laps_with_auto_duration_and_badge(): void
    {
        $this->travelTo('2026-10-10');
        Experience::factory()->create([
            'organization' => 'Kampus Contoh', 'role' => 'Mahasiswa', 'type' => ExperienceType::Pendidikan,
            'started_at' => '2023-08-01', 'ended_at' => null, 'sort' => 1,
        ]);
        Experience::factory()->create([
            'organization' => 'UKM Contoh', 'role' => 'Anggota', 'type' => ExperienceType::Organisasi,
            'started_at' => '2024-01-01', 'ended_at' => '2024-12-01', 'sort' => 2,
        ]);
        Experience::factory()->inactive()->create(['organization' => 'Disembunyikan']);

        $this->get('/')
            ->assertSeeInOrder(['LAP 01', 'Kampus Contoh', 'LAP 02', 'UKM Contoh'])
            ->assertSee('Agt 2023 – sekarang')
            ->assertSee('3 thn 3 bln')
            ->assertSee('1 thn')
            ->assertSee('Pendidikan')
            ->assertSee('Organisasi')
            ->assertDontSee('Disembunyikan');
    }

    public function test_race_progress_placeholder_without_dates_does_not_break(): void
    {
        Experience::factory()->create(['organization' => '[ISI NANTI]', 'type' => null, 'started_at' => null, 'ended_at' => null]);

        $this->get('/')->assertOk()->assertSee('LAP 01')->assertSee('[ISI NANTI]');
    }

    public function test_empty_timeline_and_garage_show_friendly_messages(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Belum ada lap yang tercatat.')
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

    public function test_race_track_becomes_scrollable_after_four_laps(): void
    {
        Experience::factory()->count(4)->create();
        $this->get('/')->assertDontSee('Geser untuk lap berikutnya');

        Experience::factory()->create();
        $this->get('/')->assertSee('Geser untuk lap berikutnya');
    }
}
