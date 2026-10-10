<?php

namespace App\Filament\Resources\MascotMessages\Schemas;

use App\Models\MascotMessage;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;

class MascotMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Text::make(fn (?MascotMessage $record) => 'Section: '.(MascotMessage::SECTIONS[$record?->section] ?? $record?->section)),
                TextInput::make('message')
                    ->label('Teks bubble')
                    ->helperText('1 kalimat pendek. Muncul saat section ini terlihat di layar.')
                    ->required()
                    ->maxLength(120),
            ]);
    }
}
