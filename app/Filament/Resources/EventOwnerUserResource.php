<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventOwnerUserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class EventOwnerUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'My Users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        Forms\Components\Select::make('roles')
                            ->multiple()
                            ->relationship('roles', 'display_name')
                            ->preload()
                            ->searchable(),
                        Forms\Components\Toggle::make('email_verified_at')
                            ->label('Email Verified')
                            ->dehydrateStateUsing(fn ($state) => $state ? now() : null)
                            ->dehydrated(fn ($state) => $state !== null),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.display_name')
                    ->badge()
                    ->separator(',')
                    ->searchable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'display_name')
                    ->multiple()
                    ->preload()
                    ->label('Filter by Role'),
                Tables\Filters\Filter::make('verified')
                    ->query(fn ($query) => $query->whereNotNull('email_verified_at'))
                    ->label('Email Verified'),
                Tables\Filters\Filter::make('unverified')
                    ->query(fn ($query) => $query->whereNull('email_verified_at'))
                    ->label('Email Not Verified'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('assign_roles')
                    ->label('Assign Roles')
                    ->icon('heroicon-o-shield-check')
                    ->form([
                        Forms\Components\Select::make('roles')
                            ->multiple()
                            ->relationship('roles', 'display_name')
                            ->preload()
                            ->searchable()
                            ->label('Select Roles'),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->roles()->sync($data['roles']);
                    }),

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
                
                // For event owners, only show users they have created or invited
                return $query->whereIn('id', function ($subQuery) use ($user) {
                    $subQuery->select('user_id')
                        ->from('user_relationships')
                        ->where('owner_id', $user->id);
                });
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
            'index' => Pages\ListEventOwnerUsers::route('/'),
            'create' => Pages\CreateEventOwnerUser::route('/create'),
            'edit' => Pages\EditEventOwnerUser::route('/{record}/edit'),
        ];
    }
} 