<?php

namespace App\Filament\Resources\UserInvitationResource\Pages;

use App\Filament\Resources\UserInvitationResource;
use App\Models\User;
use App\Models\UserRelationship;
use Filament\Resources\Pages\CreateRecord;

class CreateUserInvitation extends CreateRecord
{
    protected static string $resource = UserInvitationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Create the user first
        $user = User::create([
            'name' => $data['user']['name'],
            'email' => $data['user']['email'],
            'password' => bcrypt(str_random(12)), // Temporary password
        ]);

        // Create the relationship
        $data['owner_id'] = auth()->id();
        $data['user_id'] = $user->id;
        $data['invited_at'] = now();

        return $data;
    }
} 