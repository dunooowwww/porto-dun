<?php

namespace App\Filament\Resources\PitWallCards\Pages;

use App\Filament\Concerns\FlushesSiteCacheAfterReorder;
use App\Filament\Resources\PitWallCards\PitWallCardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPitWallCards extends ListRecords
{
    use FlushesSiteCacheAfterReorder;

    protected static string $resource = PitWallCardResource::class;

    protected ?string $subheading = 'Klik tombol urutkan di tabel lalu geser baris untuk mengatur urutan kartu di bento grid.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
