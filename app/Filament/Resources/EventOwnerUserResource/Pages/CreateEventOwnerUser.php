<?php

namespace App\Filament\Resources\EventOwnerUserResource\Pages;

use App\Filament\Resources\EventOwnerUserResource;
use App\Models\UserRelationship;
use Filament\Resources\Pages\CreateRecord;

class CreateEventOwnerUser extends CreateRecord
{
    protected static string $resource = EventOwnerUserResource::class;

    protected function afterCreate(): void
    {
        $user = $this->record;
        $owner = auth()->user();

        // Create relationship between owner and new user
        UserRelationship::create([
            'owner_id' => $owner->id,
            'user_id' => $user->id,
            'relationship_type' => 'created',
            'invited_at' => now(),
            'accepted_at' => now(), // Auto-accept since owner created the user
        ]);
    }
} 