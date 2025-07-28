<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserInvitationResource\Pages;
use App\Models\UserRelationship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserInvitationResource extends Resource
{
    protected static ?string $model = UserRelationship::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Invitations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invitation Details')
                    ->schema([
                        Forms\Components\TextInput::make('user.email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique('users', 'email'),
                        Forms\Components\TextInput::make('user.name')
                            ->label('Name')
                            ->required(),
                        Forms\Components\Select::make('relationship_type')
                            ->options([
                                'invited' => 'Invited',
                                'created' => 'Created',
                            ])
                            ->default('invited')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('relationship_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'invited' => 'warning',
                        'created' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('accepted_at')
                    ->label('Accepted')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invited_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('accepted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('relationship_type')
                    ->options([
                        'invited' => 'Invited',
                        'created' => 'Created',
                    ]),
                Tables\Filters\Filter::make('pending')
                    ->query(fn ($query) => $query->whereNull('accepted_at'))
                    ->label('Pending'),
                Tables\Filters\Filter::make('accepted')
                    ->query(fn ($query) => $query->whereNotNull('accepted_at'))
                    ->label('Accepted'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                
                if ($user->isAdmin()) {
                    return $query;
                }
                
                // For event owners, only show their invitations
                return $query->where('owner_id', $user->id);
            });
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserInvitations::route('/'),
            'create' => Pages\CreateUserInvitation::route('/create'),
            'edit' => Pages\EditUserInvitation::route('/{record}/edit'),
        ];
    }
} 