<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Event Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Event Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(4),
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('start_date')
                            ->native(false),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->native(false),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                        Forms\Components\TextInput::make('capacity')
                            ->numeric()
                            ->integer(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft'),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('events')
                            ->visibility('public'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Ticket Types')
                    ->schema([
                        Forms\Components\Repeater::make('event_ticket_types')
                            ->relationship('ticketTypes')
                            ->schema([
                                Forms\Components\Select::make('ticket_type_id')
                                    ->label('Ticket Type')
                                    ->options(
                                        \App\Models\TicketType::where('user_id', auth()->id())
                                            ->where('is_active', true)
                                            ->pluck('name', 'id')
                                    )
                                    ->required()
                                    ->searchable(),
                                Forms\Components\TextInput::make('event_price')
                                    ->label('Event Price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->required(),
                                Forms\Components\TextInput::make('quantity_available')
                                    ->label('Quantity Available')
                                    ->numeric()
                                    ->integer()
                                    ->minValue(1),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Event Team')
                    ->schema([
                        Forms\Components\Repeater::make('event_users')
                            ->relationship('assignedUsers')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('User')
                                    ->options(
                                        \App\Models\User::whereHas('userRelationships', function ($q) {
                                            $q->where('owner_id', auth()->id());
                                        })->pluck('name', 'id')
                                    )
                                    ->required()
                                    ->searchable(),
                                Forms\Components\Select::make('role')
                                    ->options([
                                        'attendee' => 'Attendee',
                                        'organizer' => 'Organizer',
                                        'staff' => 'Staff',
                                    ])
                                    ->default('attendee')
                                    ->required(),
                                Forms\Components\Toggle::make('can_view_tickets')
                                    ->label('Can View Tickets')
                                    ->default(false),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'cancelled' => 'Cancelled',
                    ]),
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
} 