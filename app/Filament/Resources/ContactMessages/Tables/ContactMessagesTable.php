<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada pesan masuk')
            ->emptyStateDescription('Pesan dari form Box, Box akan muncul di sini.')
            ->recordUrl(fn (ContactMessage $record) => ContactMessageResource::getUrl('view', ['record' => $record]))
            ->columns([
                IconColumn::make('read_at')
                    ->label('')
                    ->state(fn (ContactMessage $record) => $record->is_read)
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedEnvelopeOpen)
                    ->falseIcon(Heroicon::Envelope)
                    ->trueColor('gray')
                    ->falseColor('primary')
                    ->tooltip(fn (ContactMessage $record) => $record->is_read ? 'Sudah dibaca' : 'Belum dibaca'),
                TextColumn::make('name')
                    ->label('Nama')
                    ->description(fn (ContactMessage $record) => $record->email)
                    ->weight(fn (ContactMessage $record) => $record->is_read ? null : FontWeight::Bold)
                    ->searchable(['name', 'email']),
                TextColumn::make('message')
                    ->label('Cuplikan')
                    ->limit(80)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
                    ->dateTimeTooltip('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('read_at')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah dibaca')
                    ->falseLabel('Belum dibaca')
                    ->nullable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Baca'),
                    ContactMessageResource::markAsReadAction(),
                    ContactMessageResource::replyAction(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markAsRead')
                        ->label('Tandai dibaca')
                        ->icon(Heroicon::OutlinedCheck)
                        ->action(fn (Collection $records) => $records->each->markAsRead())
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
