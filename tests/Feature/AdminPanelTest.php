<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSiteSettings;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Filament\Resources\Experiences\ExperienceResource;
use App\Filament\Resources\Experiences\Pages\CreateExperience;
use App\Filament\Resources\MascotMessages\MascotMessageResource;
use App\Filament\Resources\MascotMessages\Pages\EditMascotMessage;
use App\Filament\Resources\PitWallCards\Pages\CreatePitWallCard;
use App\Filament\Resources\PitWallCards\Pages\EditPitWallCard;
use App\Filament\Resources\PitWallCards\Pages\ListPitWallCards;
use App\Filament\Resources\PitWallCards\PitWallCardResource;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\MascotMessage;
use App\Models\PitWallCard;
use App\Models\Project;
use App\Models\User;
use App\Support\SiteCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');
    }

    protected function signIn(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_admin_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk()->assertSee('Dun #12');
    }

    public function test_every_admin_page_loads(): void
    {
        $this->seed();
        $this->signIn();
        ContactMessage::factory()->create();

        $urls = [
            '/admin',
            '/admin/pengaturan-situs',
            PitWallCardResource::getUrl(),
            PitWallCardResource::getUrl('create'),
            PitWallCardResource::getUrl('edit', ['record' => PitWallCard::first()]),
            ExperienceResource::getUrl(),
            ExperienceResource::getUrl('create'),
            ExperienceResource::getUrl('edit', ['record' => Experience::first()]),
            ProjectResource::getUrl(),
            ProjectResource::getUrl('create'),
            ProjectResource::getUrl('edit', ['record' => Project::first()]),
            MascotMessageResource::getUrl(),
            MascotMessageResource::getUrl('edit', ['record' => MascotMessage::first()]),
            ContactMessageResource::getUrl(),
            ContactMessageResource::getUrl('view', ['record' => ContactMessage::first()]),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_site_settings_page_saves_and_refreshes_cache(): void
    {
        $this->seed();
        $this->signIn();

        $this->assertSame('Mahasiswa SI di siang hari, ngulik AI sampai lights out.', setting('hero_tagline'));

        Livewire::test(ManageSiteSettings::class)
            ->assertFormSet(['display_name' => 'Dun', 'full_name' => 'Ardan Haryanto Putra'])
            ->fillForm([
                'full_name' => 'Dun Test',
                'hero_tagline' => 'Tagline baru',
                'contact_whatsapp' => '6281234567890',
                'hero_tech_icons' => [['name' => 'Laravel', 'icon' => null]],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Dun Test', setting('full_name'));
        $this->assertSame('Tagline baru', setting('hero_tagline'));
        $this->assertSame([['name' => 'Laravel', 'icon' => null]], array_values(setting('hero_tech_icons')));
    }

    public function test_site_settings_validates_contact_fields(): void
    {
        $this->seed();
        $this->signIn();

        Livewire::test(ManageSiteSettings::class)
            ->fillForm([
                'contact_whatsapp' => '0812-3456',
                'contact_email' => 'bukan-email',
                'contact_linkedin' => 'linkedin saya',
            ])
            ->call('save')
            ->assertHasFormErrors(['contact_whatsapp', 'contact_email', 'contact_linkedin']);
    }

    public function test_only_four_pit_wall_cards_can_be_active(): void
    {
        $this->signIn();
        PitWallCard::factory()->count(4)->create();
        $lastSort = PitWallCard::max('sort');

        Livewire::test(CreatePitWallCard::class)
            ->fillForm(['sector_label' => 'SECTOR 5', 'title' => 'Kelima', 'body' => 'Isi', 'is_active' => true])
            ->call('create')
            ->assertHasFormErrors(['is_active']);

        Livewire::test(CreatePitWallCard::class)
            ->fillForm(['sector_label' => 'SECTOR 5', 'title' => 'Cadangan', 'body' => 'Isi', 'is_active' => false])
            ->call('create')
            ->assertHasNoFormErrors();

        // Mengedit kartu yang sudah aktif tetap boleh.
        Livewire::test(EditPitWallCard::class, ['record' => PitWallCard::active()->first()->getRouteKey()])
            ->fillForm(['title' => 'Judul baru'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(4, PitWallCard::where('is_active', true)->count());
        $this->assertSame($lastSort + 1, PitWallCard::firstWhere('title', 'Cadangan')->sort, 'Kartu baru diletakkan di urutan terakhir');
    }

    public function test_pit_wall_cards_can_be_reordered_and_flush_cache(): void
    {
        $this->signIn();
        [$a, $b] = PitWallCard::factory()->count(2)->sequence(['sort' => 1], ['sort' => 2])->create();
        $version = SiteCache::version();

        Livewire::test(ListPitWallCards::class)->call('reorderTable', [(string) $b->id, (string) $a->id]);

        $this->assertSame([$b->id, $a->id], PitWallCard::orderBy('sort')->pluck('id')->all());
        $this->assertGreaterThan($version, SiteCache::version());
    }

    public function test_experience_form_requires_type_and_valid_dates(): void
    {
        $this->signIn();

        Livewire::test(CreateExperience::class)
            ->fillForm([
                'organization' => 'UKM Robotika',
                'role' => 'Anggota',
                'started_at' => '2024-05-01',
                'ended_at' => '2024-01-01',
            ])
            ->call('create')
            ->assertHasFormErrors(['type' => 'required', 'ended_at']);
    }

    public function test_project_is_created_with_slug_from_title(): void
    {
        $this->signIn();

        Livewire::test(CreateProject::class)
            ->fillForm(['title' => 'Sistem Antrian Klinik'])
            ->assertFormSet(['slug' => 'sistem-antrian-klinik'])
            ->fillForm(['status' => 'published', 'tech_stack' => ['Laravel', 'MySQL']])
            ->call('create')
            ->assertHasNoFormErrors();

        $project = Project::firstWhere('slug', 'sistem-antrian-klinik');
        $this->assertNotNull($project?->published_at);
        $this->assertSame(['Laravel', 'MySQL'], $project->tech_stack);
    }

    public function test_project_slug_must_be_unique(): void
    {
        $this->signIn();
        Project::factory()->create(['slug' => 'dashboard']);

        Livewire::test(CreateProject::class)
            ->fillForm(['title' => 'Dashboard Lain', 'slug' => 'dashboard', 'status' => 'draft'])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);
    }

    public function test_mascot_messages_can_only_be_edited(): void
    {
        $this->seed();
        $this->signIn();
        $message = MascotMessage::firstWhere('section', 'hero');

        $this->assertFalse(MascotMessageResource::canCreate());
        $this->assertFalse(MascotMessageResource::canDelete($message));
        $this->assertArrayNotHasKey('create', MascotMessageResource::getPages());

        Livewire::test(EditMascotMessage::class, ['record' => $message->getRouteKey()])
            ->fillForm(['message' => 'Gas!'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Gas!', $message->fresh()->message);
    }

    public function test_inbox_badge_and_mark_as_read(): void
    {
        $this->signIn();
        $first = ContactMessage::factory()->create();
        $second = ContactMessage::factory()->create();
        ContactMessage::factory()->read()->create();

        $this->assertSame('2', ContactMessageResource::getNavigationBadge());

        // Membuka pesan otomatis menandainya dibaca.
        Livewire::test(ViewContactMessage::class, ['record' => $first->getRouteKey()]);
        $this->assertTrue($first->fresh()->is_read);

        Livewire::test(ListContactMessages::class)->callTableAction('markAsRead', $second);
        $this->assertTrue($second->fresh()->is_read);

        $this->assertNull(ContactMessageResource::getNavigationBadge());
        $this->assertFalse(ContactMessageResource::canCreate());
    }

    public function test_replaced_and_deleted_uploads_are_removed_from_disk(): void
    {
        Storage::disk('uploads')->put('pit-wall/lama.webp', 'x');
        Storage::disk('uploads')->put('pit-wall/baru.webp', 'x');
        $card = PitWallCard::factory()->create(['image' => 'pit-wall/lama.webp']);

        $card->update(['image' => 'pit-wall/baru.webp']);
        Storage::disk('uploads')->assertMissing('pit-wall/lama.webp');

        $card->delete();
        Storage::disk('uploads')->assertMissing('pit-wall/baru.webp');
    }

    public function test_deleting_project_removes_gallery_files(): void
    {
        Storage::disk('uploads')->put('projects/gallery/a.webp', 'x');
        $project = Project::factory()->create();
        $project->images()->create(['path' => 'projects/gallery/a.webp', 'sort' => 1]);

        $project->delete();

        Storage::disk('uploads')->assertMissing('projects/gallery/a.webp');
    }

    public function test_saving_content_flushes_front_page_cache(): void
    {
        $version = SiteCache::version();
        PitWallCard::factory()->create();
        $this->assertGreaterThan($version, $version = SiteCache::version());

        MascotMessage::factory()->create();
        $this->assertGreaterThan($version, SiteCache::version());
    }
}
