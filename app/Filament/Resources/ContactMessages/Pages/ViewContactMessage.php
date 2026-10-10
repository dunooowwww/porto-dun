<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Membuka pesan = otomatis ditandai dibaca.
        /** @var ContactMessage $message */
        $message = $this->getRecord();
        $message->markAsRead();
    }

    protected function getHeaderActions(): array
    {
        return [
            ContactMessageResource::replyAction(),
            DeleteAction::make(),
        ];
    }
}
