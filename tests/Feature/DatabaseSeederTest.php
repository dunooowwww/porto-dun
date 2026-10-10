<?php

namespace Tests\Feature;

use App\Enums\ExperienceCategory;
use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\MascotMessage;
use App\Models\PitWallCard;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\SiteSetting;
use App\Support\Icon;
use Database\Seeders\CvContentSeeder;
use Database\Seeders\ExperienceSeeder;
use Database\Seeders\PitWallCardSeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');
    }

    public function test_seeder_fills_all_content_tables(): void
    {
        $this->seed();

        $this->assertSame(4, PitWallCard::active()->count());
        $this->assertSame(12, Experience::count());
        $this->assertSame(7, Project::published()->count());
        $this->assertEqualsCanonicalizing(array_keys(MascotMessage::SECTIONS), MascotMessage::pluck('section')->all());
        $this->assertSame(0, ContactMessage::count());

        $this->assertSame('Dun', setting('display_name'));
        $this->assertSame(12, setting('racing_number'));
        $this->assertSame('Ardan Haryanto Putra', setting('full_name'));
    }

    public function test_placeholder_seeders_do_not_invent_data(): void
    {
        $this->seed([SiteSettingSeeder::class, ExperienceSeeder::class, ProjectSeeder::class]);

        foreach (['contact_email', 'contact_whatsapp', 'contact_linkedin', 'contact_github', 'contact_instagram', 'cv_path'] as $key) {
            $this->assertNull(setting($key), "{$key} harus kosong sampai diisi Dun");
        }

        $this->assertSame('[ISI NANTI]', setting('full_name'));
        $project = Project::first();
        $this->assertSame('[ISI NANTI]', $project->title);
        $this->assertSame([], $project->tech_stack);
        $this->assertNull($project->year);
        $this->assertNull($project->github_url);
        $this->assertNull(Experience::first()->started_at);
    }

    public function test_cv_content_replaces_placeholders(): void
    {
        $this->seed([SiteSettingSeeder::class, PitWallCardSeeder::class, ExperienceSeeder::class, ProjectSeeder::class]);
        $this->seed(CvContentSeeder::class);

        $this->assertSame(0, Experience::where('organization', '[ISI NANTI]')->count());
        $this->assertSame(0, Project::where('title', '[ISI NANTI]')->count());
        $this->assertSame(0, PitWallCard::where('body', '[ISI NANTI]')->count());
        $this->assertSame(12, Experience::count());
        $this->assertSame(7, Project::count());
    }

    public function test_cv_content_fills_contact_files_and_chart(): void
    {
        $this->seed();

        $this->assertSame('ardanputra405@gmail.com', setting('contact_email'));
        $this->assertSame('6281391381661', setting('contact_whatsapp'));
        $this->assertSame('https://www.linkedin.com/in/ardanptrr12/', setting('contact_linkedin'));
        $this->assertNull(setting('contact_instagram'));
        Storage::disk('uploads')->assertExists(setting('cv_path'));

        foreach (setting('hero_tech_icons') as $item) {
            $this->assertTrue(Icon::allowedForContent($item['icon']), $item['icon']);
        }

        foreach (Project::with('images')->get() as $project) {
            Storage::disk('uploads')->assertExists($project->thumbnail);
            $project->images->each(fn ($image) => Storage::disk('uploads')->assertExists($image->path));
        }
        $this->assertSame(15, ProjectImage::count());

        // 5 profesional + 3 organisasi di chart; pendidikan/lomba/sertifikasi tanpa kategori.
        $this->assertSame(5, Experience::where('category', ExperienceCategory::Professional)->count());
        $this->assertSame(3, Experience::where('category', ExperienceCategory::Organizational)->count());
        $this->assertSame(4, Experience::whereNull('category')->count());

        $this->get('/')->assertOk()->assertSee('Machine Learning Engineer Intern')->assertSee('Download CV');
        $this->get('/projects/graphie')->assertOk()->assertSee('LightRAG');
    }

    public function test_cv_content_keeps_admin_edits(): void
    {
        $this->seed();
        SiteSetting::set('hero_greeting', 'Halo, kenalin aku *Ardan*');
        SiteSetting::set('contact_email', 'lain@example.com');
        Experience::where('role', 'IT Intern')->first()->update(['achievement' => 'Diedit di admin']);
        Project::where('slug', 'graphie')->first()->update(['title' => 'Graphie baru']);

        $this->seed(CvContentSeeder::class);

        $this->assertSame('Halo, kenalin aku *Ardan*', setting('hero_greeting'));
        $this->assertSame('lain@example.com', setting('contact_email'));
        $this->assertSame('Diedit di admin', Experience::where('role', 'IT Intern')->first()->achievement);
        $this->assertSame('Graphie baru', Project::where('slug', 'graphie')->first()->title);
        $this->assertSame(12, Experience::count());
        $this->assertSame(7, Project::count());
        $this->assertSame(15, ProjectImage::count());
    }

    public function test_pose_images_are_copied_to_uploads_disk(): void
    {
        $this->seed();

        foreach (PitWallCard::all() as $card) {
            Storage::disk('uploads')->assertExists($card->image);
        }
    }

    public function test_running_seeder_again_keeps_admin_edits(): void
    {
        $this->seed();
        SiteSetting::set('hero_tagline', 'Diedit di admin');
        PitWallCard::first()->update(['title' => 'Judul baru']);

        $this->seed();

        $this->assertSame('Diedit di admin', setting('hero_tagline'));
        $this->assertSame('Judul baru', PitWallCard::first()->title);
        $this->assertSame(4, PitWallCard::count());
        $this->assertSame(7, Project::count());
    }
}
