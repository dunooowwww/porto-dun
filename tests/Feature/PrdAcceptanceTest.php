<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature test wajib sesuai PRD bagian 12 (Definition of Done).
 * Test yang lebih rinci untuk tiap fitur ada di file test lainnya.
 */
class PrdAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');
        $this->seed();
        RateLimiter::clear('contact:127.0.0.1');
    }

    public function test_halaman_home_200(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_detail_project_200(): void
    {
        $project = Project::factory()->create(['slug' => 'project-terbit']);

        $this->get('/projects/'.$project->slug)->assertOk()->assertSee($project->title);
    }

    public function test_slug_salah_404(): void
    {
        $this->get('/projects/slug-yang-tidak-ada')->assertNotFound();
    }

    public function test_form_kontak_tersimpan(): void
    {
        $this->post('/kontak', [
            'name' => 'Rekruter',
            'email' => 'rekruter@example.com',
            'message' => 'Halo, mau ngobrol soal magang.',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_messages', ['email' => 'rekruter@example.com']);
    }

    public function test_form_honeypot_ditolak(): void
    {
        $this->post('/kontak', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'Beli sekarang, diskon besar!',
            'website' => 'http://spam.example',
        ]);

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_admin_butuh_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/pesan-masuk')->assertRedirect('/admin/login');
    }
}
