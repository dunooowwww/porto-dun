<?php

namespace App\Filament\Resources\Experiences\Schemas;

use App\Enums\ExperienceCategory;
use App\Enums\ExperienceType;
use App\Filament\Support\Upload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('organization')
                    ->label('Nama organisasi / instansi')
                    ->placeholder('UPN "Veteran" Yogyakarta')
                    ->required()
                    ->maxLength(150),
                TextInput::make('role')
                    ->label('Peran / posisi')
                    ->placeholder('Mahasiswa Sistem Informasi')
                    ->required()
                    ->maxLength(150),
                Select::make('type')
                    ->label('Jenis')
                    ->options(ExperienceType::class)
                    ->required()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        // Isi kategori otomatis dari jenis (tetap bisa diubah manual).
                        $type = $state instanceof ExperienceType ? $state : ExperienceType::tryFrom((string) $state);
                        $set('category', $type?->defaultCategory()?->value);
                    }),
                Select::make('category')
                    ->label('Kategori')
                    ->options(ExperienceCategory::class)
                    ->placeholder('Tidak tampil di chart')
                    ->helperText('Profesional → tab "Main race", Organisasi → tab "Sprint". Kosongkan untuk Pendidikan, Lomba, Sertifikasi.')
                    ->native(false),
                Toggle::make('is_active')
                    ->label('Aktif (tampil di website)')
                    ->default(true)
                    ->inline(false),
                DatePicker::make('started_at')
                    ->label('Mulai')
                    ->helperText('Yang dihitung hanya bulan & tahun; tanggal boleh bebas.')
                    ->displayFormat('F Y')
                    ->native(false)
                    ->required(),
                DatePicker::make('ended_at')
                    ->label('Selesai')
                    ->helperText('Kosongkan jika masih berjalan (tampil "sekarang").')
                    ->displayFormat('F Y')
                    ->native(false)
                    ->afterOrEqual('started_at'),
                Textarea::make('achievement')
                    ->label('Pencapaian')
                    ->helperText('Opsional, 1 kalimat. Jangan tulis angka yang belum pasti.')
                    ->rows(2)
                    ->maxLength(300)
                    ->columnSpanFull(),
                Upload::image('logo', 'experiences')
                    ->label('Logo (opsional)'),
            ]);
    }
}
