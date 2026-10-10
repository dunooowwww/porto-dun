<?php

namespace App\Filament\Resources\MascotMessages\Pages;

use App\Filament\Resources\MascotMessages\MascotMessageResource;
use Filament\Resources\Pages\ListRecords;

class ListMascotMessages extends ListRecords
{
    protected static string $resource = MascotMessageResource::class;

    protected ?string $subheading = 'Satu bubble per section. Teksnya bisa diubah, barisnya tidak bisa ditambah atau dihapus.';
}
