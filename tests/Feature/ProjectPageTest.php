<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');
        $this->seed();
        Project::query()->get()->each->delete();
    }

    public function test_project_detail_page_returns_200_with_case_study_in_order(): void
    {
        $project = Project::factory()->create([
            'title' => 'Sistem Antrian',
            'slug' => 'sistem-antrian',
            'problem' => 'Antrian manual bikin bingung.',
            'role' => 'Backend developer',
            'process' => '<p>Mulai dari wawancara.</p>',
            'result' => 'Waktu tunggu lebih jelas.',
            'year' => 2025,
        ]);
        $project->images()->create(['path' => 'projects/gallery/a.webp', 'caption' => 'Halaman antrian', 'sort' => 1]);

        $this->get('/projects/sistem-antrian')
            ->assertOk()
            ->assertSee('Sistem Antrian')
            ->assertSeeInOrder(['Masalah', 'Antrian manual', 'Peran &amp; proses', 'Mulai dari wawancara', 'Hasil', 'Waktu tunggu', 'Galeri', 'Halaman antrian'], false)
            ->assertSee('Kembali ke Garage');
    }

    public function test_wrong_slug_returns_404(): void
    {
        $this->get('/projects/tidak-ada')->assertNotFound();
    }

    public function test_project_meta_and_og_come_from_project(): void
    {
        Storage::disk('uploads')->put('projects/thumb.webp', 'x');
        Project::factory()->create([
            'title' => 'Dashboard Analitik',
            'slug' => 'dashboard',
            'excerpt' => 'Ringkasan untuk Google.',
            'thumbnail' => 'projects/thumb.webp',
        ]);

        $this->get('/projects/dashboard')
            ->assertSee('<title>Dashboard Analitik – ', false)
            ->assertSee('<meta name="description" content="Ringkasan untuk Google.">', false)
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('projects/thumb.webp', false)
            ->assertDontSee('noindex');
    }

    public function test_previous_and_next_follow_garage_order(): void
    {
        Project::factory()->create(['title' => 'Satu', 'slug' => 'satu', 'sort' => 1]);
        Project::factory()->create(['title' => 'Dua', 'slug' => 'dua', 'sort' => 2]);
        Project::factory()->create(['title' => 'Tiga', 'slug' => 'tiga', 'sort' => 3]);

        $this->get('/projects/dua')
            ->assertSee('P2')
            ->assertSee('Sebelumnya · P1')
            ->assertSee(route('projects.show', 'satu'), false)
            ->assertSee('Berikutnya · P3')
            ->assertSee(route('projects.show', 'tiga'), false);

        $this->get('/projects/satu')->assertDontSee('Sebelumnya')->assertSee('Berikutnya · P2');
        $this->get('/projects/tiga')->assertSee('Sebelumnya · P2')->assertDontSee('Berikutnya');
    }

    public function test_draft_is_hidden_from_visitors_but_previewable_by_admin(): void
    {
        Project::factory()->draft()->create(['title' => 'Masih Draft', 'slug' => 'masih-draft']);

        $this->get('/projects/masih-draft')->assertNotFound();

        $this->actingAs(User::factory()->create())
            ->get('/projects/masih-draft')
            ->assertOk()
            ->assertSee('hanya terlihat olehmu')
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_process_html_is_sanitized(): void
    {
        Project::factory()->create([
            'slug' => 'aman',
            'process' => '<p onclick="alert(1)">Aman</p><script>alert("xss")</script>',
        ]);

        $this->get('/projects/aman')
            ->assertSee('Aman')
            ->assertDontSee('<script>alert("xss")</script>', false)
            ->assertDontSee('onclick', false);
    }

    public function test_empty_case_study_shows_friendly_message(): void
    {
        Project::factory()->create([
            'slug' => 'kosong', 'problem' => null, 'role' => null, 'process' => null, 'result' => null,
        ]);

        $this->get('/projects/kosong')->assertOk()->assertSee('Case study project ini belum diisi.');
    }
}
