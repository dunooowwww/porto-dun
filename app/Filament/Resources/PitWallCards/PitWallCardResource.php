<?php

namespace App\Filament\Resources\PitWallCards;

use App\Filament\Resources\PitWallCards\Pages\CreatePitWallCard;
use App\Filament\Resources\PitWallCards\Pages\EditPitWallCard;
use App\Filament\Resources\PitWallCards\Pages\ListPitWallCards;
use App\Filament\Resources\PitWallCards\Schemas\PitWallCardForm;
use App\Filament\Resources\PitWallCards\Tables\PitWallCardsTable;
use App\Models\PitWallCard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PitWallCardResource extends Resource
{
    protected static ?string $model = PitWallCard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'kartu Pit Wall';

    protected static ?string $pluralModelLabel = 'Pit Wall Cards';

    protected static ?string $slug = 'pit-wall-cards';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PitWallCardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PitWallCardsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPitWallCards::route('/'),
            'create' => CreatePitWallCard::route('/create'),
            'edit' => EditPitWallCard::route('/{record}/edit'),
        ];
    }
}
