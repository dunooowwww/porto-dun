<?php

namespace Tests\Feature;

use App\Enums\ExperienceType;
use App\Models\Experience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_duration_is_counted_in_inclusive_calendar_months(): void
    {
        $experience = Experience::factory()->create([
            'started_at' => '2023-08-01',
            'ended_at' => '2024-10-01',
        ]);

        $this->assertSame(15, $experience->duration_in_months);
        $this->assertSame('1 thn 3 bln', $experience->duration_label);
        $this->assertSame('Agt 2023 – Okt 2024', $experience->period_label);
        $this->assertFalse($experience->is_current);
    }

    public function test_current_experience_runs_until_now(): void
    {
        $this->travelTo('2026-10-10');

        $experience = Experience::factory()->current()->create(['started_at' => '2023-08-01']);

        $this->assertTrue($experience->is_current);
        $this->assertSame('3 thn 3 bln', $experience->duration_label);
        $this->assertSame('Agt 2023 – sekarang', $experience->period_label);
    }

    public function test_short_and_whole_year_durations(): void
    {
        $oneMonth = Experience::factory()->create(['started_at' => '2025-03-01', 'ended_at' => '2025-03-20']);
        $twoYears = Experience::factory()->create(['started_at' => '2022-01-01', 'ended_at' => '2023-12-01']);

        $this->assertSame('1 bln', $oneMonth->duration_label);
        $this->assertSame('2 thn', $twoYears->duration_label);
    }

    public function test_placeholder_without_dates_has_no_duration(): void
    {
        $experience = Experience::factory()->create(['type' => null, 'started_at' => null, 'ended_at' => null]);

        $this->assertNull($experience->duration_label);
        $this->assertNull($experience->period_label);
        $this->assertNull($experience->type);
    }

    public function test_type_is_cast_to_enum_and_active_scope_filters(): void
    {
        Experience::factory()->create(['type' => ExperienceType::Lomba, 'sort' => 2]);
        Experience::factory()->create(['sort' => 1]);
        Experience::factory()->inactive()->create();

        $this->assertSame(ExperienceType::Lomba, Experience::where('type', 'lomba')->first()->type);
        $this->assertSame([1, 2], Experience::active()->pluck('sort')->all());
    }
}
