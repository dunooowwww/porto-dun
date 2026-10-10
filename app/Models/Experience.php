<?php

namespace App\Models;

use App\Enums\ExperienceType;
use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

#[Fillable(['organization', 'role', 'type', 'started_at', 'ended_at', 'achievement', 'logo', 'sort', 'is_active'])]
class Experience extends Model
{
    /** @use HasFactory<ExperienceFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => ExperienceType::class,
            'started_at' => 'date',
            'ended_at' => 'date',
            'sort' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort')->orderBy('started_at');
    }

    /** Masih berjalan jika tanggal selesai kosong. */
    protected function isCurrent(): Attribute
    {
        return Attribute::get(fn () => $this->ended_at === null);
    }

    /**
     * Durasi dalam bulan kalender, dihitung inklusif: Agustus 2023 – Oktober 2024 = 15 bulan.
     */
    protected function durationInMonths(): Attribute
    {
        return Attribute::get(function (): ?int {
            if ($this->started_at === null) {
                return null;
            }

            $end = $this->ended_at ?? Carbon::now();

            return max(1, ($end->year * 12 + $end->month) - ($this->started_at->year * 12 + $this->started_at->month) + 1);
        });
    }

    /** Contoh: "1 thn 3 bln", "8 bln", "2 thn". Null jika tanggal mulai belum diisi. */
    protected function durationLabel(): Attribute
    {
        return Attribute::get(function (): ?string {
            $months = $this->duration_in_months;

            if ($months === null) {
                return null;
            }

            $parts = array_filter([
                intdiv($months, 12) ? intdiv($months, 12).' thn' : null,
                $months % 12 ? ($months % 12).' bln' : null,
            ]);

            return implode(' ', $parts);
        });
    }

    /** Contoh: "Agt 2023 – sekarang". */
    protected function periodLabel(): Attribute
    {
        return Attribute::get(function (): ?string {
            if ($this->started_at === null) {
                return null;
            }

            $format = fn (Carbon $date) => $date->locale(app()->getLocale())->translatedFormat('M Y');

            return $format($this->started_at).' – '.($this->ended_at ? $format($this->ended_at) : 'sekarang');
        });
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::get(fn () => upload_url($this->logo));
    }
}
