<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * M7: memastikan markup animasi terpasang di tempat yang benar dan tetap aman tanpa JS.
 * Perilaku animasinya sendiri dicek manual di browser (lihat penjelasan M7).
 */
class MotionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');
        $this->seed();
    }

    public function test_lights_out_loading_screen_is_on_home_page_only(): void
    {
        $this->get('/')
            ->assertSee('class="lights-out', false)
            ->assertSee("sessionStorage.getItem('dun12-lights-out')", false)
            ->assertSee('prefers-reduced-motion: reduce', false)
            ->assertSee('Klik atau tekan Esc untuk lewati');

        $this->get('/projects/'.Project::first()->slug)->assertDontSee('class="lights-out', false);
        $this->get('/tidak-ada')->assertDontSee('class="lights-out', false);
    }

    public function test_loading_screen_is_hidden_unless_script_adds_class(): void
    {
        // Overlay hanya tampil lewat kelas .lights-on di <html>, yang dipasang oleh skrip.
        // Tanpa JS kelas itu tidak pernah ada, jadi halaman tidak pernah terkunci.
        $html = $this->get('/')->getContent();

        $this->assertStringNotContainsString('<html class="lights-on"', $html);
        $this->assertStringContainsString('setTimeout(done, 1700)', $html);
        $this->assertSame(5, substr_count($html, 'class="lamp '));
    }

    public function test_sections_have_reveal_markers_with_small_stagger(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertGreaterThanOrEqual(8, substr_count($html, 'data-reveal'));
        $this->assertStringContainsString('--reveal-delay: 60ms', $html);
        $this->assertStringNotContainsString('--reveal-delay: 420ms', $html, 'Stagger dibatasi maksimal 6 langkah');
        // Hero sengaja tidak ikut animasi masuk.
        $this->assertDoesNotMatchRegularExpression('/<section id="home"[^>]*data-reveal/', $html);
    }

    public function test_race_track_has_a_moving_car_when_there_are_laps(): void
    {
        $this->get('/')->assertSee('x-data="raceCar"', false)->assertSee('x-ref="car"', false);

        Experience::query()->get()->each->delete(); // lewat model, supaya cache halaman depan ikut dibersihkan
        $this->get('/')->assertDontSee('x-ref="car"', false);
    }

    public function test_success_message_has_checkered_flag_animation(): void
    {
        $this->followingRedirects()
            ->post('/kontak', ['name' => 'Tes', 'email' => 'tes@example.com', 'message' => 'Pesan untuk tes animasi.'])
            ->assertSee('flag-wave', false);
    }
}
