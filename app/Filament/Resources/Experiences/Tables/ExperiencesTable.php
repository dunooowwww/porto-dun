<?php

namespace App\Filament\Resources\Experiences\Tables;

use App\Enums\ExperienceCategory;
use App\Enums\ExperienceType;
use App\Models\Experience;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ExperiencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->emptyStateHeading('Belum ada pengalaman')
            ->columns([
                TextColumn::make('sort')
                    ->label('Lap')
                    ->formatStateUsing(fn (int $state) => 'LAP '.str_pad((string) $state, 2, '0', STR_PAD_LEFT))
                    ->fontFamily('mono')
                    ->color('gray'),
                TextColumn::make('organization')
                    ->label('Organisasi / instansi')
                    ->description(fn (Experience $record) => $record->role)
                    ->searchable(['organization', 'role'])
                    ->wrap(),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->placeholder('Belum diisi'),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Tidak di chart'),
                TextColumn::make('period_label')
                    ->label('Periode')
                    ->description(fn (Experience $record) => $record->duration_label)
                    ->placeholder('Belum diisi'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options(ExperienceType::class),
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(ExperienceCategory::class),
                TernaryFilter::make('is_active')
                    ->label('Aktif'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
