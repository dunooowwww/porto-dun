<?php

namespace App\Enums;

enum ExperienceType: string
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
