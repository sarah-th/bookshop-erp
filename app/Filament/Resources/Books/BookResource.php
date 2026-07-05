<?php

namespace App\Filament\Resources\Books;

use App\Enums\BookCategory;
use App\Enums\BookLevel;
use App\Filament\Resources\Books\Pages;
use App\Models\Book;
use App\Models\Publisher;
use App\Models\Supplier;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use HasAdminOnlyDelete;
use App\Filament\Support\FilamentAuth;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    public static function getModelLabel(): string
    {
        return __('Book');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Books');
    }

    public static function getNavigationLabel(): string
    {
        return __('Books');
    }

    public static function getNavigationGroup(): string
    {
        return __('Management');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('Book Information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('isbn')
                            ->label(__('ISBN'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('supplier_id')
                            ->label(__('Supplier'))
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        Select::make('publisher_id')
                            ->label(__('Publisher'))
                            ->relationship('publisher', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        Select::make('level')
                            ->label(__('Level'))
                            ->options(BookLevel::class)
                            ->required()
                            ->searchable()
                            ->native(false),

                        Select::make('category')
                            ->label(__('Category'))
                            ->options(BookCategory::class)
                            ->required()
                            ->searchable()
                            ->native(false),

                        TextInput::make('current_quantity')
                            ->label(__('Current Quantity'))
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->minValue(0),

                        TextInput::make('cost')
                            ->label(__('Cost'))
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required()
                            ->minValue(0),

                        Select::make('currency_id')
                            ->label(__('Currency'))
                            ->relationship('currency', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required(),
                                TextInput::make('code')
                                    ->label(__('Code'))
                                    ->required()
                                    ->maxLength(3),
                            ]),

                        DatePicker::make('last_purchase')
                            ->label(__('Last Purchase'))
                            ->native(false),

                        DatePicker::make('last_sell')
                            ->label(__('Last Sell'))
                            ->native(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('isbn')
                    ->label(__('ISBN'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('supplier.name')
                    ->label(__('Supplier'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('publisher.name')
                    ->label(__('Publisher'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('level')
                    ->label(__('Level'))
                    ->badge()
                    ->color('info'),

                TextColumn::make('category')
                    ->label(__('Category'))
                    ->badge()
                    ->color('primary'),

                TextColumn::make('current_quantity')
                    ->label(__('Current Quantity'))
                    ->sortable()
                    ->color(fn (int $state): string => $state <= 5 ? 'danger' : 'success'),

                TextColumn::make('cost')
                    ->label(__('Cost'))
                    ->formatStateUsing(fn ($record) => $record->currency 
                        ? $record->currency->code . ' ' . number_format($record->cost, 2)
                        : 'EGP' . number_format($record->cost, 2))
                    ->sortable(),

                TextColumn::make('last_purchase')
                    ->label(__('Last Purchase'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('last_sell')
                    ->label(__('Last Sell'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('supplier_id')
                    ->label(__('Supplier'))
                    ->relationship('supplier', 'name'),

                SelectFilter::make('publisher_id')
                    ->label(__('Publisher'))
                    ->relationship('publisher', 'name'),

                SelectFilter::make('level')
                    ->label(__('Level'))
                    ->options(BookLevel::class),

                SelectFilter::make('category')
                    ->label(__('Category'))
                    ->options(BookCategory::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()->visible(fn () => FilamentAuth::isAdmin()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => FilamentAuth::isAdmin()),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}