<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewSite')
                ->label('Lihat website')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->url(url('/'), shouldOpenInNewTab: true),
            Action::make('createProject')
                ->label('Tambah project')
                ->icon(Heroicon::OutlinedPlus)
                ->url(ProjectResource::getUrl('create')),
        ];
    }
}
