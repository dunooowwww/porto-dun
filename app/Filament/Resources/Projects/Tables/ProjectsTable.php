<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectStatus;
use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->emptyStateHeading('Belum ada project')
            ->columns([
                TextColumn::make('sort')
                    ->label('Posisi')
                    ->formatStateUsing(fn (int $state) => 'P'.$state)
                    ->fontFamily('mono')
                    ->color('gray'),
                ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->disk('uploads'),
                TextColumn::make('title')
                    ->label('Judul')
                    ->description(fn ($record) => '/projects/'.$record->slug)
                    ->searchable(['title', 'slug'])
                    ->wrap(),
                TextColumn::make('tech_stack')
                    ->label('Tech stack')
                    ->badge()
                    ->color('gray')
                    ->limitList(3),
                TextColumn::make('year')
                    ->label('Tahun')
                    ->placeholder('—'),
                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ProjectStatus::class),
            ])
            ->recordActions([
                ProjectResource::previewAction(),
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
