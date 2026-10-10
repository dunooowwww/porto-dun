<?php

namespace App\Filament\Resources\PitWallCards\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PitWallCardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->paginated(false)
            ->emptyStateHeading('Belum ada kartu')
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('uploads'),
                TextColumn::make('sector_label')
                    ->label('Sector')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                TextColumn::make('body')
                    ->label('Isi')
                    ->limit(60)
                    ->wrap(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
