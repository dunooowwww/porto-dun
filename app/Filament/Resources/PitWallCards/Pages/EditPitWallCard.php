<?php

namespace App\Filament\Resources\PitWallCards\Pages;

use App\Filament\Resources\PitWallCards\PitWallCardResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPitWallCard extends EditRecord
{
    protected static string $resource = PitWallCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
