<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->copyable()
                            ->copyMessage('Email disalin'),
                        TextEntry::make('created_at')
                            ->label('Dikirim')
                            ->dateTime('d M Y, H:i'),
                        TextEntry::make('message')
                            ->label('Pesan')
                            ->columnSpanFull()
                            ->prose(),
                        TextEntry::make('ip')
                            ->label('IP pengirim')
                            ->placeholder('—')
                            ->color('gray'),
                        TextEntry::make('read_at')
                            ->label('Dibaca')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Belum'),
                    ]),
            ]);
    }
}
