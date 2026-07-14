<?php
// app/Filament/Resources/AccountStatements/ClientStatementResource.php

namespace App\Filament\Resources\AccountStatements;

use App\Enums\PaymentMethod;
use App\Enums\StatementType;
use App\Filament\Resources\AccountStatements\Pages\ClientStatements;
use App\Filament\Support\FilamentAuth;
use App\Models\AccountStatement;
use App\Models\Client;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClientStatementResource extends Resource
{
    protected static ?string $model = AccountStatement::class;
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function getModelLabel(): string       { return __('Client Statement'); }
    public static function getPluralModelLabel(): string  { return __('Client Statements'); }
    public static function getNavigationLabel(): string   { return __('Client Statements'); }
    public static function getNavigationGroup(): string   { return __('Sales'); }
    public static function getNavigationSort(): ?int      { return 10; }

    // Scope to client statements only
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('type', StatementType::CLIENT->value);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('Statement Information'))
                    ->schema([
                        Hidden::make('type')->default(StatementType::CLIENT->value),

                        Select::make('client_id')
                            ->label(__('Client'))
                            ->options(fn () => Client::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->native(false),

                        DatePicker::make('date')
                            ->label(__('Date'))
                            ->default(now())
                            ->required()
                            ->native(false),

                        TextInput::make('amount')
                            ->label(__('Amount'))
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->prefix('EGP'),

                        Select::make('currency_id')
                            ->label(__('Currency'))
                            ->options(fn () => \App\Models\Currency::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->native(false),

                        Select::make('payment_method')
                            ->label(__('Payment Method'))
                            ->options(PaymentMethod::class)
                            ->required()
                            ->native(false),

                        TextInput::make('reference')
                            ->label(__('Reference'))
                            ->placeholder(__('Cheque no., transfer ref., etc.'))
                            ->maxLength(255),

                        Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),

                TextColumn::make('date')
                    ->label(__('Date'))
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('client.name')
                    ->label(__('Client'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->sortable()
                    ->weight('bold')
                    ->formatStateUsing(fn ($state, $record) =>
                        ($record->currency?->symbol ?? 'EGP') . ' ' . number_format($state, 2)
                    ),

                TextColumn::make('currency.code')
                    ->label(__('Currency'))
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label(__('Payment Method'))
                    ->badge()
                    ->color(fn (PaymentMethod $state) => $state->getColor())
                    ->sortable(),

                TextColumn::make('reference')
                    ->label(__('Reference'))
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('notes')
                    ->label(__('Notes'))
                    ->placeholder('-')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('client_id')
                    ->label(__('Client'))
                    ->relationship('client', 'name'),

                SelectFilter::make('payment_method')
                    ->label(__('Payment Method'))
                    ->options(PaymentMethod::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn () => FilamentAuth::isAdmin()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => FilamentAuth::isAdmin()),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ClientStatements\ListClientStatements::route('/'),
            'create' => ClientStatements\CreateClientStatement::route('/create'),
            'view'   => ClientStatements\ViewClientStatement::route('/{record}'),
            'edit'   => ClientStatements\EditClientStatement::route('/{record}/edit'),
        ];
    }
}