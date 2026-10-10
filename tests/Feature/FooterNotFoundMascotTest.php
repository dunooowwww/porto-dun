<?php

namespace Tests\Feature;

use App\Models\MascotMessage;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FooterNotFoundMascotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');
        $this->seed();
    }

    public function test_footer_has_checkered_line_text_and_current_year(): void
    {
        $this->travelTo('2027-03-01');
        SiteSetting::set('full_name', 'Dun Lengkap');

        $this->get('/')
            ->assertSee('<footer', false)
            ->assertSee('checkered', false)
            ->assertSee('Dirakit dengan kopi dan semangat race day.')
            ->assertSee('© 2027 Dun Lengkap');
    }

    public function test_footer_falls_back_to_display_name_when_full_name_is_placeholder(): void
    {
        SiteSetting::set('full_name', '[ISI NANTI]');

        $this->get('/')->assertSee('© '.now()->year.' Dun')->assertDontSee('© '.now()->year.' [ISI NANTI]');
    }

    public function test_custom_404_page(): void
    {
        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('DNF – Did Not Find')
            ->assertSee('Halaman ini mogok di tikungan.')
            ->assertSee('Balik ke pit')
            ->assertSee('images/dnf.webp', false)
            ->assertSee('noindex', false);

        $this->get('/projects/slug-salah')->assertNotFound()->assertSee('DNF – Did Not Find');
    }

    public function test_mascot_messages_come_from_database(): void
    {
        MascotMessage::firstWhere('section', 'garage')->update(['message' => 'Bubble garage dari admin']);

        $this->get('/')
            ->assertSee('x-data="mascot(', false)
            ->assertSee('Bubble garage dari admin')
            ->assertDontSee('Sembunyikan maskot');
    }

    public function test_mascot_is_hidden_when_there_are_no_messages(): void
    {
        MascotMessage::query()->delete();

        $this->get('/')->assertOk()->assertDontSee('x-data="mascot(', false);
    }

    public function test_mascot_only_appears_on_home_page(): void
    {
        $project = Project::first();

        $this->get('/projects/'.$project->slug)->assertOk()->assertDontSee('x-data="mascot(', false);
    }
}
