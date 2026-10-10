<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_setting_returns_value_or_default(): void
    {
        SiteSetting::set('hero_tagline', 'Lights out');
        SiteSetting::set('contact_email', null);

        $this->assertSame('Lights out', setting('hero_tagline'));
        $this->assertSame('cadangan', setting('contact_email', 'cadangan'));
        $this->assertSame('cadangan', setting('tidak_ada', 'cadangan'));
    }

    public function test_setting_keeps_arrays_and_numbers(): void
    {
        SiteSetting::set('racing_number', 12);
        SiteSetting::set('hero_tech_icons', [['name' => 'Laravel', 'icon' => 'laravel']]);

        $this->assertSame(12, setting('racing_number'));
        $this->assertSame([['name' => 'Laravel', 'icon' => 'laravel']], setting('hero_tech_icons'));
    }

    public function test_setting_is_cached_and_flushed_on_save(): void
    {
        SiteSetting::set('footer_text', 'lama');
        $this->assertSame('lama', setting('footer_text'));

        // Ubah langsung di database tanpa event model: cache masih menyimpan nilai lama.
        SiteSetting::query()->where('key', 'footer_text')->update(['value' => json_encode('diam-diam')]);
        $this->assertSame('lama', setting('footer_text'));

        // Simpan lewat model (seperti admin nanti): cache dibuang, nilai baru terbaca.
        SiteSetting::set('footer_text', 'baru');
        $this->assertSame('baru', setting('footer_text'));
    }

    public function test_cache_is_flushed_on_delete(): void
    {
        $setting = SiteSetting::set('city', 'Yogyakarta');
        $this->assertSame('Yogyakarta', setting('city'));

        $setting->delete();
        $this->assertNull(setting('city'));
    }
}
