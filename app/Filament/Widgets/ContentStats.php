<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\Experiences\ExperienceResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Project;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Widget dashboard (PRD A1): pesan belum dibaca, project terbit, jumlah pengalaman.
 */
class ContentStats extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $unread = ContactMessage::unread()->count();

        return [
            Stat::make('Pesan belum dibaca', $unread)
                ->icon(Heroicon::OutlinedInboxArrowDown)
                ->description($unread ? 'Klik untuk membaca' : 'Semua sudah dibaca')
                ->color($unread ? 'danger' : 'gray')
                ->url(ContactMessageResource::getUrl()),
            Stat::make('Project terbit', Project::published()->count())
                ->icon(Heroicon::OutlinedWrenchScrewdriver)
                ->description('Tampil di Garage')
                ->url(ProjectResource::getUrl()),
            Stat::make('Pengalaman', Experience::count())
                ->icon(Heroicon::OutlinedFlag)
                ->description('Checkpoint di Race Progress')
                ->url(ExperienceResource::getUrl()),
        ];
    }
}
