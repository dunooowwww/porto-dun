<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\MascotMessage;
use App\Models\PitWallCard;
use App\Models\Project;
use App\Models\SiteSetting;
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
        $this->assertSame(3, Experience::count());
        $this->assertSame(3, Project::published()->count());
        $this->assertEqualsCanonicalizing(array_keys(MascotMessage::SECTIONS), MascotMessage::pluck('section')->all());
        $this->assertSame(0, ContactMessage::count());

        $this->assertSame('Dun', setting('display_name'));
        $this->assertSame(12, setting('racing_number'));
        $this->assertSame('[ISI NANTI]', setting('full_name'));
    }

    public function test_seeder_does_not_invent_contact_data(): void
    {
        $this->seed();

        foreach (['contact_email', 'contact_whatsapp', 'contact_linkedin', 'contact_github', 'contact_instagram', 'cv_path'] as $key) {
            $this->assertNull(setting($key), "{$key} harus kosong sampai diisi Dun");
        }

        $project = Project::first();
        $this->assertSame('[ISI NANTI]', $project->title);
        $this->assertSame([], $project->tech_stack);
        $this->assertNull($project->year);
        $this->assertNull($project->github_url);
        $this->assertNull(Experience::first()->started_at);
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
        $this->assertSame(3, Project::count());
    }
}
