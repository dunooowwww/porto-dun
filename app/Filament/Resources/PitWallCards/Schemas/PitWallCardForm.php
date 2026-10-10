<?php

namespace App\Filament\Resources\PitWallCards\Schemas;

use App\Filament\Support\Upload;
use App\Models\PitWallCard;
use Closure;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PitWallCardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('sector_label')
                    ->label('Label sector')
                    ->placeholder('SECTOR 1')
                    ->helperText('Label kecil di pojok kartu.')
                    ->required()
                    ->maxLength(30),
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(80),
                Textarea::make('body')
                    ->label('Isi')
                    ->helperText('1–2 kalimat.')
                    ->required()
                    ->rows(3)
                    ->maxLength(400)
                    ->columnSpanFull(),
                Upload::image('image', 'pit-wall')
                    ->label('Gambar pose'),
                Toggle::make('is_active')
                    ->label('Aktif (tampil di website)')
                    ->helperText('Maksimal '.PitWallCard::MAX_ACTIVE.' kartu aktif.')
                    ->default(true)
                    ->rule(fn (?PitWallCard $record) => function (string $attribute, mixed $value, Closure $fail) use ($record) {
                        if (! $value) {
                            return;
                        }

                        $otherActive = PitWallCard::query()
                            ->where('is_active', true)
                            ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                            ->count();

                        if ($otherActive >= PitWallCard::MAX_ACTIVE) {
                            $fail('Sudah ada '.PitWallCard::MAX_ACTIVE.' kartu aktif. Nonaktifkan salah satu kartu lain dulu.');
                        }
                    }),
            ]);
    }
}
