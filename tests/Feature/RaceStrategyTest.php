<?php

namespace Tests\Feature;

use App\Enums\ExperienceCategory;
use App\Enums\ExperienceType;
use App\Filament\Resources\Experiences\Pages\CreateExperience;
use App\Filament\Resources\Experiences\Pages\ListExperiences;
use App\Models\Experience;
use App\Models\User;
use App\Support\FrontData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RaceStrategyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');
        $this->seed();
        Experience::query()->get()->each->delete();
        $this->travelTo('2026-10-10');
    }

    protected function stint(string $organization, ExperienceType $type, string $start, ?string $end = null): Experience
    {
        return Experience::factory()->create([
            'organization' => $organization,
            'type' => $type,
            'category' => $type->defaultCategory(),
            'started_at' => $start,
            'ended_at' => $end,
        ]);
    }

    public function test_type_maps_to_default_category(): void
    {
        $this->assertSame(ExperienceCategory::Professional, ExperienceType::Magang->defaultCategory());
        $this->assertSame(ExperienceCategory::Professional, ExperienceType::Kerja->defaultCategory());
        $this->assertSame(ExperienceCategory::Organizational, ExperienceType::Organisasi->defaultCategory());
        $this->assertSame(ExperienceCategory::Organizational, ExperienceType::Kepanitiaan->defaultCategory());
        $this->assertNull(ExperienceType::Pendidikan->defaultCategory());
        $this->assertNull(ExperienceType::Lomba->defaultCategory());
        $this->assertNull(ExperienceType::Sertifikasi->defaultCategory());
    }

    public function test_chart_has_two_tabs_with_their_own_stints_and_year_range(): void
    {
        $this->stint('Studio Contoh', ExperienceType::Magang, '2023-07-01', '2023-12-01');
        $this->stint('Agensi Contoh', ExperienceType::Kerja, '2025-01-01');
        $this->stint('UKM Contoh', ExperienceType::Organisasi, '2024-02-01', '2024-05-01');

        $tabs = FrontData::raceStrategy();

        $this->assertSame(['professional', 'organizational'], array_column($tabs, 'key'));
        $this->assertSame([2023, 2024, 2025, 2026], $tabs[0]['years']);
        $this->assertSame([2024], $tabs[1]['years']);
        $this->assertCount(2, $tabs[0]['stints']);
        $this->assertCount(1, $tabs[1]['stints']);

        $this->get('/')
            ->assertSee('Main race')
            ->assertSee('Profesional')
            ->assertSee('Sprint')
            ->assertSee('Organisasi')
            ->assertSee('>2 stints<', false)
            ->assertSee('>1 stint<', false)
            ->assertSee('role="tablist"', false)
            ->assertSee('id="race-panel-professional"', false)
            ->assertSee('id="race-panel-organizational"', false)
            ->assertSeeInOrder(['Studio Contoh', 'Agensi Contoh']);
    }

    public function test_bar_position_is_computed_from_dates(): void
    {
        // Rentang 2024–2025 = 24 bulan. Jan–Jun 2024 = 6 bulan dari bulan ke-0.
        $this->stint('A', ExperienceType::Magang, '2024-01-01', '2024-06-01');
        $this->stint('B', ExperienceType::Kerja, '2025-07-01', '2025-12-01');

        $stints = FrontData::raceStrategy()[0]['stints'];

        $this->assertEquals(0, $stints[0]['left']);
        $this->assertEquals(25, $stints[0]['width']);
        $this->assertEquals(75, $stints[1]['left']);
        $this->assertEquals(25, $stints[1]['width']);
    }

    public function test_education_competition_and_certification_are_not_in_chart(): void
    {
        $this->stint('Kampus Contoh', ExperienceType::Pendidikan, '2023-08-01');
        $this->stint('Lomba Contoh', ExperienceType::Lomba, '2024-11-01', '2024-11-01');
        $this->stint('Sertifikat Contoh', ExperienceType::Sertifikasi, '2025-01-01', '2025-01-01');
        $this->stint('Magang Contoh', ExperienceType::Magang, '2024-06-01', '2024-09-01');

        $this->get('/')
            ->assertSee('Magang Contoh')
            ->assertDontSee('Kampus Contoh')
            ->assertDontSee('Lomba Contoh')
            ->assertDontSee('Sertifikat Contoh');
    }

    public function test_tab_without_data_is_hidden_and_default_falls_back_to_organizational(): void
    {
        $this->stint('UKM Contoh', ExperienceType::Organisasi, '2024-02-01', '2024-05-01');

        $this->get('/')
            ->assertDontSee('id="race-tab-professional"', false)
            ->assertSee('id="race-tab-organizational"', false)
            ->assertSee(", 'organizational')\"", false);
    }

    public function test_default_tab_is_professional_when_it_has_data(): void
    {
        $this->stint('Magang Contoh', ExperienceType::Magang, '2024-06-01', '2024-09-01');
        $this->stint('UKM Contoh', ExperienceType::Organisasi, '2024-02-01', '2024-05-01');

        $this->get('/')->assertSee(", 'professional')\"", false);
    }

    public function test_empty_chart_shows_friendly_message(): void
    {
        $this->stint('Kampus Contoh', ExperienceType::Pendidikan, '2023-08-01');

        $this->get('/')->assertOk()->assertSee('Belum ada stint yang tercatat.')->assertDontSee('role="tablist"', false);
    }

    public function test_ongoing_stint_runs_until_now_and_is_marked(): void
    {
        $this->stint('Agensi Contoh', ExperienceType::Kerja, '2025-01-01');

        $stint = FrontData::raceStrategy()[0]['stints'][0];

        $this->assertTrue($stint['current']);
        $this->assertSame('Jan 2025 – sekarang', $stint['period']);
        $this->assertSame('1 thn 10 bln', $stint['duration']);
    }

    public function test_admin_form_has_category_and_table_filter(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateExperience::class)
            ->fillForm(['type' => 'magang'])
            ->assertFormSet(['category' => ExperienceCategory::Professional])
            ->fillForm(['organization' => 'Startup', 'role' => 'Magang', 'started_at' => '2024-06-01'])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(ExperienceCategory::Professional, Experience::firstWhere('organization', 'Startup')->category);

        $this->stint('UKM Contoh', ExperienceType::Organisasi, '2024-02-01');

        Livewire::test(ListExperiences::class)
            ->filterTable('category', 'organizational')
            ->assertCanSeeTableRecords(Experience::where('category', 'organizational')->get())
            ->assertCanNotSeeTableRecords(Experience::where('category', 'professional')->get());
    }
}
