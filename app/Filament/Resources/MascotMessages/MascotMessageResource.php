<?php

namespace App\Filament\Resources\MascotMessages;

use App\Filament\Resources\MascotMessages\Pages\EditMascotMessage;
use App\Filament\Resources\MascotMessages\Pages\ListMascotMessages;
use App\Filament\Resources\MascotMessages\Schemas\MascotMessageForm;
use App\Filament\Resources\MascotMessages\Tables\MascotMessagesTable;
use App\Models\MascotMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Bubble chat maskot: satu baris per section, hanya bisa diedit (PRD A6).
 */
class MascotMessageResource extends Resource
{
    protected static ?string $model = MascotMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleOvalLeftEllipsis;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Mascot Messages';

    protected static ?string $modelLabel = 'bubble maskot';

    protected static ?string $pluralModelLabel = 'Mascot Messages';

    public static function form(Schema $schema): Schema
    {
        return MascotMessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MascotMessagesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMascotMessages::route('/'),
            'edit' => EditMascotMessage::route('/{record}/edit'),
        ];
    }
}
