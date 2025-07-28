<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketTypeResource\Pages;
use App\Models\TicketType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TicketTypeResource extends Resource
{
    protected static ?string $model = TicketType::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Event Management';

    protected static ?string $navigationLabel = 'Ticket Types';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),

                Forms\Components\Section::make('Ticket Type Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., VIP Pass, General Admission, Group Package'),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->placeholder('Describe what this ticket type includes'),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->placeholder('0.00'),
                        Forms\Components\TextInput::make('max_persons_per_ticket')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->default(1)
                            ->label('Max Persons Per Ticket')
                            ->helperText('Set to 1 for individual tickets, higher for group packages'),
                        Forms\Components\TextInput::make('available_quantity')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->label('Available Quantity')
                            ->helperText('Leave empty for unlimited tickets'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive ticket types won\'t be available for events'),
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
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_persons_per_ticket')
                    ->label('Group Size')
                    ->formatStateUsing(fn (int $state): string => $state === 1 ? 'Individual' : $state . ' persons')
                    ->sortable(),
                Tables\Columns\TextColumn::make('available_quantity')
                    ->label('Quantity')
                    ->formatStateUsing(fn ($state): string => $state ? $state : 'Unlimited')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('max_persons_per_ticket')
                    ->label('Group Size')
                    ->options([
                        1 => 'Individual',
                        2 => '2 persons',
                        3 => '3 persons',
                        4 => '4 persons',
                        5 => '5+ persons',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
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
            ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', auth()->id()));
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
            'index' => Pages\ListTicketTypes::route('/'),
            'create' => Pages\CreateTicketType::route('/create'),
            'edit' => Pages\EditTicketType::route('/{record}/edit'),
        ];
    }
} 