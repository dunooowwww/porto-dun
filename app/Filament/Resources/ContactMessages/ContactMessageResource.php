<?php

namespace App\Filament\Resources\ContactMessages;

use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Filament\Resources\ContactMessages\Schemas\ContactMessageInfolist;
use App\Filament\Resources\ContactMessages\Tables\ContactMessagesTable;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Pesan dari form Box, Box (PRD A7). Hanya baca, tandai dibaca, balas via email, hapus.
 */
class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Pesan Masuk';

    protected static ?string $modelLabel = 'pesan';

    protected static ?string $pluralModelLabel = 'Pesan Masuk';

    protected static ?string $slug = 'pesan-masuk';

    protected static ?string $recordTitleAttribute = 'name';

    public static function infolist(Schema $schema): Schema
    {
        return ContactMessageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactMessagesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    /** Badge jumlah pesan belum dibaca di menu navigasi. */
    public static function getNavigationBadge(): ?string
    {
        $unread = ContactMessage::unread()->count();

        return $unread > 0 ? (string) $unread : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Belum dibaca';
    }

    public static function markAsReadAction(): Action
    {
        return Action::make('markAsRead')
            ->label('Tandai dibaca')
            ->icon(Heroicon::OutlinedCheck)
            ->color('gray')
            ->visible(fn (ContactMessage $record) => ! $record->is_read)
            ->action(fn (ContactMessage $record) => $record->markAsRead());
    }

    public static function replyAction(): Action
    {
        return Action::make('reply')
            ->label('Balas via email')
            ->icon(Heroicon::OutlinedEnvelope)
            ->url(fn (ContactMessage $record) => 'mailto:'.$record->email.'?subject='.rawurlencode('Re: pesan dari portofolio Dun #12'));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
        ];
    }
}
