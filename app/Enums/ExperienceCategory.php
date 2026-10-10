<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Kategori pengalaman untuk chart Race Strategy. Pendidikan, Lomba, dan Sertifikasi
 * tidak punya kategori, jadi tidak tampil di chart.
 */
enum ExperienceCategory: string implements HasLabel
{
    case Professional = 'professional';
    case Organizational = 'organizational';

    public function label(): string
    {
        return match ($this) {
            self::Professional => 'Profesional',
            self::Organizational => 'Organisasi',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    /** Label tab di chart, mis. "MAIN RACE · Profesional". */
    public function raceLabel(): string
    {
        return match ($this) {
            self::Professional => 'Main race',
            self::Organizational => 'Sprint',
        };
    }
}
