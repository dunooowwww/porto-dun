<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\SiteSetting;
use App\Support\Icon;
use App\Support\ImageVariants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');
        $this->seed();
    }

    public function test_sitemap_lists_home_and_published_projects_only(): void
    {
        Project::query()->get()->each->delete();
        Project::factory()->create(['slug' => 'terbit']);
        Project::factory()->draft()->create(['slug' => 'masih-draft']);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.route('home').'</loc>', false)
            ->assertSee('<loc>'.route('projects.show', 'terbit').'</loc>', false)
            ->assertDontSee('masih-draft');
    }

    public function test_sitemap_updates_after_project_is_published(): void
    {
        $draft = Project::factory()->draft()->create(['slug' => 'baru-terbit']);
        $this->get('/sitemap.xml')->assertDontSee('baru-terbit');

        $draft->update(['status' => 'published']);
        $this->get('/sitemap.xml')->assertSee('baru-terbit');
    }

    public function test_robots_txt_points_to_sitemap_and_hides_admin(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap: '.route('sitemap'));
    }

    public function test_head_has_seo_tags_favicon_and_font_preload(): void
    {
        $this->get('/')
            ->assertSee('<title>', false)
            ->assertSee('name="description"', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('property="og:image" content="'.asset('images/og-default.jpg').'"', false)
            ->assertSee('name="twitter:card" content="summary_large_image"', false)
            ->assertSee('href="'.asset('favicon.ico').'"', false)
            ->assertSee('apple-touch-icon', false)
            ->assertSee('rel="preload" as="font"', false);
    }

    public function test_uploaded_og_image_is_absolute_url(): void
    {
        Storage::forgetDisk('uploads'); // pakai disk asli (Storage::fake memakai URL /storage/...)
        SiteSetting::set('og_image', 'seo/og.jpg');

        $this->get('/')->assertSee('property="og:image" content="'.url('/uploads/seo/og.jpg').'"', false);
    }

    public function test_upload_urls_do_not_depend_on_app_url(): void
    {
        Storage::forgetDisk('uploads'); // pakai disk asli (Storage::fake memakai URL /storage/...)
        config(['app.url' => 'https://salah.example']);

        $this->assertSame('/uploads/pit-wall/ai-ml.webp', upload_url('pit-wall/ai-ml.webp'));
    }

    public function test_static_files_exist(): void
    {
        foreach (['favicon.ico', 'images/favicon-32.png', 'images/apple-touch-icon.png', 'images/og-default.jpg', 'images/mascot-head-160.webp'] as $file) {
            $this->assertFileExists(public_path($file));
            $this->assertGreaterThan(0, filesize(public_path($file)), "{$file} tidak boleh kosong");
        }
    }

    public function test_large_uploads_get_responsive_webp_variants(): void
    {
        $image = imagecreatetruecolor(2000, 1000);
        ob_start();
        imagejpeg($image);
        Storage::disk('uploads')->put('projects/besar.jpg', ob_get_clean());

        $project = Project::factory()->create(['thumbnail' => 'projects/besar.jpg']);

        foreach ([480, 960, 1600] as $width) {
            Storage::disk('uploads')->assertExists("projects/besar-{$width}w.webp");
        }
        $this->assertStringContainsString('besar-480w.webp 480w', ImageVariants::srcset('projects/besar.jpg'));
        $this->get('/projects/'.$project->slug)->assertSee('srcset=', false);

        $project->delete();
        Storage::disk('uploads')->assertMissing('projects/besar-480w.webp');
    }

    public function test_small_uploads_get_no_bigger_variants(): void
    {
        $image = imagecreatetruecolor(300, 200);
        ob_start();
        imagepng($image);
        Storage::disk('uploads')->put('pit-wall/kecil.png', ob_get_clean());

        ImageVariants::generate('pit-wall/kecil.png');

        Storage::disk('uploads')->assertMissing('pit-wall/kecil-480w.webp');
        $this->assertNull(ImageVariants::srcset('pit-wall/kecil.png'));
    }

    public function test_brand_logo_icons_are_not_rendered(): void
    {
        SiteSetting::set('hero_tech_icons', [['name' => 'Laravel', 'icon' => 'brand-laravel'], ['name' => 'MySQL', 'icon' => 'database']]);

        $html = $this->get('/')->assertSee('Laravel')->getContent();
        preg_match('/<ul[^>]*aria-label="Tech yang aku pakai">(.*?)<\/ul>/s', $html, $list);

        $this->assertSame(1, substr_count($list[1], '<svg'), 'Hanya ikon "database" yang dirender');
        $this->assertFalse(Icon::allowedForContent('brand-laravel'));
        $this->assertTrue(Icon::allowedForContent('database'));
    }

    public function test_hero_image_is_not_lazy_but_other_images_are(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertMatchesRegularExpression('/<img src="[^"]*hero\.webp"[^>]*fetchpriority="high"/s', $html);
        preg_match_all('/<img [^>]*>/s', $html, $images);
        foreach ($images[0] as $img) {
            if (! str_contains($img, 'hero.webp')) {
                $this->assertStringContainsString('loading="lazy"', $img);
            }
        }
    }
}
