<?php

namespace App\Filament\Resources\MascotMessages\Pages;

use App\Filament\Resources\MascotMessages\MascotMessageResource;
use Filament\Resources\Pages\EditRecord;

class EditMascotMessage extends EditRecord
{
    protected static string $resource = MascotMessageResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
