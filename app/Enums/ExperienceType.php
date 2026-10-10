<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ExperienceType: string implements HasLabel
{
    case Pendidikan = 'pendidikan';
    case Organisasi = 'organisasi';
    case Kepanitiaan = 'kepanitiaan';
    case Lomba = 'lomba';
    case Magang = 'magang';
    case Kerja = 'kerja';
    case Sertifikasi = 'sertifikasi';

    public function label(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    /**
     * Kategori bawaan untuk chart Race Strategy. Null = tidak tampil di chart.
     */
    public function defaultCategory(): ?ExperienceCategory
    {
        return match ($this) {
            self::Magang, self::Kerja => ExperienceCategory::Professional,
            self::Organisasi, self::Kepanitiaan => ExperienceCategory::Organizational,
            self::Pendidikan, self::Lomba, self::Sertifikasi => null,
        };
    }

    /**
     * Pilihan untuk dropdown admin: ['pendidikan' => 'Pendidikan', ...].
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $type) => [$type->value => $type->label()])->all();
    }
}
