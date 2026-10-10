<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSiteSettings;
use App\Models\PitWallCard;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\Icon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');
        $this->seed();
    }

    public function test_home_page_renders_seo_meta_from_settings(): void
    {
        SiteSetting::set('meta_title', 'Dun #12 – Portofolio');
        SiteSetting::set('meta_description', 'Deskripsi untuk Google.');

        $this->get('/')
            ->assertOk()
            ->assertSee('<title>Dun #12 – Portofolio</title>', false)
            ->assertSee('<meta name="description" content="Deskripsi untuk Google.">', false)
            ->assertSee('property="og:image"', false)
            ->assertSee('name="twitter:card"', false)
            ->assertSee('rel="canonical"', false);
    }

    public function test_hero_shows_profile_settings(): void
    {
        SiteSetting::set('hero_greeting', 'Halo, aku Dun');
        SiteSetting::set('hero_tagline', 'Tagline dari admin');

        $this->get('/')
            ->assertSee('Halo, aku', false)
            ->assertSee('Tagline dari admin')
            ->assertSee('#12')
            ->assertSee('Lihat Garage')
            ->assertSee('href="#garage"', false);
    }

    public function test_navbar_links_to_every_section(): void
    {
        $response = $this->get('/');

        foreach (['home', 'pit-wall', 'race', 'garage', 'box-box'] as $id) {
            $response->assertSee('#'.$id.'"', false)->assertSee('id="'.$id.'"', false);
        }
    }

    public function test_download_cv_button_only_appears_when_cv_exists(): void
    {
        $this->get('/')->assertDontSee('Download CV');
        $this->get('/cv')->assertNotFound();

        Storage::disk('uploads')->put('files/cv.pdf', '%PDF');
        SiteSetting::set('cv_path', 'files/cv.pdf');

        $this->get('/')->assertSee('Download CV');
        $this->get('/cv')->assertRedirectContains('files/cv.pdf');
    }

    public function test_tech_icons_render_valid_icons_only(): void
    {
        SiteSetting::set('hero_tech_icons', [
            ['name' => 'Laravel', 'icon' => 'brand-laravel'],
            ['name' => 'Rahasia', 'icon' => 'tidak-ada-ikonnya'],
        ]);

        $html = $this->get('/')->assertSee('Laravel')->assertSee('Rahasia')->getContent();

        preg_match('/<ul[^>]*aria-label="Tech yang aku pakai">(.*?)<\/ul>/s', $html, $techList);
        $this->assertSame(1, substr_count($techList[1] ?? '', '<svg'), 'Hanya ikon yang valid yang dirender');
        $this->assertTrue(Icon::exists('brand-laravel'));
        $this->assertFalse(Icon::exists('tidak-ada-ikonnya'));
        $this->assertFalse(Icon::exists('../../etc/passwd'));
    }

    public function test_pit_wall_shows_only_active_cards_in_order(): void
    {
        PitWallCard::query()->delete();
        PitWallCard::factory()->create(['title' => 'Kartu Kedua', 'sort' => 2]);
        PitWallCard::factory()->create(['title' => 'Kartu Pertama', 'sort' => 1]);
        PitWallCard::factory()->inactive()->create(['title' => 'Kartu Nonaktif']);

        $this->get('/')
            ->assertSeeInOrder(['Kartu Pertama', 'Kartu Kedua'])
            ->assertDontSee('Kartu Nonaktif');
    }

    public function test_pit_wall_without_cards_shows_friendly_message(): void
    {
        PitWallCard::query()->delete();

        $this->get('/')->assertOk()->assertSee('Kartu Pit Wall belum diisi.');
    }

    public function test_bento_grid_uses_uneven_card_widths(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertSame(2, substr_count($html, 'md:col-span-2'));
        $this->assertSame(2, substr_count($html, 'md:col-span-1'));
    }

    public function test_home_page_updates_after_admin_edit(): void
    {
        $this->get('/')->assertSee('AI/ML Enthusiast');

        PitWallCard::firstWhere('title', 'AI/ML Enthusiast')->update(['title' => 'Judul Baru dari Admin']);

        $this->get('/')->assertSee('Judul Baru dari Admin')->assertDontSee('AI/ML Enthusiast');
    }

    public function test_admin_rejects_unknown_tech_icon(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ManageSiteSettings::class)
            ->fillForm(['hero_tech_icons' => [['name' => 'Laravel', 'icon' => 'laravell']]])
            ->call('save')
            ->assertHasFormErrors();
    }
}
