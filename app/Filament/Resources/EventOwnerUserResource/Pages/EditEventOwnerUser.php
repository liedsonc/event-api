<?php

namespace App\Filament\Resources\EventOwnerUserResource\Pages;

use App\Filament\Resources\EventOwnerUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventOwnerUser extends EditRecord
{
    protected static string $resource = EventOwnerUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
} 