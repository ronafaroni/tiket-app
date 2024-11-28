<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingTransactionResource\Pages;
use App\Filament\Resources\BookingTransactionResource\RelationManagers;
use App\Models\BookingTransaction;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;

class BookingTransactionResource extends Resource
{
    protected static ?string $model = BookingTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Customer';

    public static function getNavigationBadge(): ?string
    {
        return (string) BookingTransaction::where('is_paid', false)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Product and Price')
                        ->schema([
                            Forms\Components\Select::make('ticket_id')
                                ->relationship('ticket', 'name')
                                ->searchable()
                                ->preload()
                                ->reactive()
                                ->required()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $ticket = Ticket::find($state);
                                    $set('price', $ticket ? $ticket->price : 0);
                                }),

                            Forms\Components\TextInput::make('total_participant')
                                ->numeric()
                                ->prefix('People')
                                ->reactive()
                                ->required()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $price = $get('price');
                                    $subTotal = $state * $price;
                                    $totalPpn = $subTotal * 0.1;
                                    $totalAmount = $subTotal + $totalPpn;

                                    $set('total_amount', $totalAmount);
                                }),

                            Forms\Components\TextInput::make('total_amount')
                                ->numeric()
                                ->required()
                                ->prefix('IDR')
                                ->readOnly()
                                ->helperText('Harga sudah termasuk PPN 11%'),
                        ]),

                    Forms\Components\Wizard\Step::make('Customer Information')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('phone_number')
                                ->tel()
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('booking_trx_id')
                                ->required()
                                ->maxLength(255),
                        ]),

                    Forms\Components\Wizard\Step::make('Payment Information')
                        ->schema([
                            ToggleButtons::make('is_paid')
                                ->label('Apakah sudah membayar')
                                ->boolean()
                                ->grouped()
                                ->icons([
                                    true => 'heroicon-o-face-smile',
                                    false => 'heroicon-o-face-frown',
                                ])
                                ->required(),

                            Forms\Components\FileUpload::make('proof')
                                ->required()
                                ->image()
                                ->directory('proof'),

                            Forms\Components\DatePicker::make('started_at')
                                ->required(),

                        ])
                ])
                    ->columnSpan('full')
                    ->columns(1)
                    ->skippable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('ticket.thumbnail')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('booking_trx_id')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->prefix('IDR ')
                    ->numeric(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean()
                    ->label('Terverifikasi'),
            ])
            ->filters([
                SelectFilter::make('ticket_id')
                    ->label('Ticket')
                    ->relationship('ticket', 'name')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->action(function (BookingTransaction $record) {
                        $record->is_paid = true;
                        $record->save();

                        Notification::make()
                            ->title('Booking Transaction Approved')
                            ->success()
                            ->body('The booking transaction has been approved.')
                            ->send();
                    })
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(BookingTransaction $record) => !$record->is_paid),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListBookingTransactions::route('/'),
            'create' => Pages\CreateBookingTransaction::route('/create'),
            'edit' => Pages\EditBookingTransaction::route('/{record}/edit'),
        ];
    }
}
