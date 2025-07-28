<?php

namespace App\Filament\Resources\EventOwnerUserResource\Pages;

use App\Filament\Resources\EventOwnerUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventOwnerUsers extends ListRecords
{
    protected static string $resource = EventOwnerUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
} 