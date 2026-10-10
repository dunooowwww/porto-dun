<?php

namespace App\Filament\Resources\MascotMessages\Tables;

use App\Models\MascotMessage;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MascotMessagesTable
{
    public static function configure(Table $table): Table
    {
        // Urutkan sesuai urutan section di halaman, bukan abjad.
        $order = array_keys(MascotMessage::SECTIONS);

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderByRaw(
                'CASE section '.collect($order)->map(fn ($s, $i) => "WHEN '{$s}' THEN {$i}")->implode(' ').' ELSE 99 END'
            ))
            ->paginated(false)
            ->columns([
                TextColumn::make('section')
                    ->label('Section')
                    ->formatStateUsing(fn (string $state) => MascotMessage::SECTIONS[$state] ?? $state)
                    ->badge()
                    ->color('gray'),
                TextColumn::make('message')
                    ->label('Teks bubble')
                    ->wrap(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
