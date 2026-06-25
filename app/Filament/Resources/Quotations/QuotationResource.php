<?php

namespace App\Filament\Resources\Quotations;

use App\Enums\QuotationStatus;
use App\Filament\Resources\Quotations\Pages;
use App\Models\Book;
use App\Models\Quotation;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    public static function getModelLabel(): string
    {
        return __('Quotation');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Quotations');
    }

    public static function getNavigationLabel(): string
    {
        return __('Quotations');
    }

    public static function getNavigationGroup(): string
    {
        return __('Sales');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('Quotation Information'))
                    ->schema([
                        TextInput::make('quotation_number')
                            ->label(__('Quotation Number'))
                            ->default(fn () => Quotation::generateNumber())
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(),

                        Select::make('client_id')
                            ->label(__('Client'))
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        DatePicker::make('date')
                            ->label(__('Date'))
                            ->default(now())
                            ->required()
                            ->native(false),

                        DatePicker::make('valid_until')
                            ->label(__('Valid Until'))
                            ->native(false),

                        Select::make('status')
                            ->label(__('Status'))
                            ->options(QuotationStatus::class)
                            ->default('draft')
                            ->required()
                            ->native(false),

                        Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make(__('Quotation Items'))
                    ->schema([
                        Repeater::make('items')
                            ->label('')
                            ->relationship()
                            ->minItems(1)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                            ->schema([
                                Select::make('book_id')
                                    ->label(__('Book'))
                                    ->options(Book::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        if ($state) {
                                            $book = Book::find($state);

                                            // Validate that all items share the same currency
                                            $items = $get('../../items') ?? [];
                                            $existingCurrencyIds = collect($items)
                                                ->filter(fn ($item) => !empty($item['book_id']))
                                                ->map(fn ($item) => Book::find($item['book_id'])?->currency_id)
                                                ->filter()
                                                ->unique();

                                            if (
                                                $existingCurrencyIds->count() > 1 ||
                                                ($existingCurrencyIds->count() === 1 && ! $existingCurrencyIds->contains($book?->currency_id))
                                            ) {
                                                $set('book_id', null);
                                                $set('unit_price', 0);
                                                $set('net_value', 0);
                                                self::updateItemNetValue($get, $set);

                                                Notification::make()
                                                    ->title(__('Currency Mismatch'))
                                                    ->body(__('All books in a quotation must have the same currency.'))
                                                    ->danger()
                                                    ->send();

                                                return;
                                            }

                                            $set('unit_price', $book?->cost ?? 0);
                                            self::updateItemNetValue($get, $set);
                                        }
                                    })
                                    ->columnSpan(3),

                                TextInput::make('quantity')
                                    ->label(__('Quantity'))
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1)
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateItemNetValue($get, $set);
                                    })
                                    ->columnSpan(1),

                                TextInput::make('unit_price')
                                    ->label(__('Unit Price'))
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateItemNetValue($get, $set);
                                    })
                                    ->columnSpan(2),

                                TextInput::make('discount')
                                    ->label(__('Discount %'))
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateItemNetValue($get, $set);
                                    })
                                    ->columnSpan(2),

                                TextInput::make('net_value')
                                    ->label(__('Net Value'))
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(2),
                            ])
                            ->columns(10)
                            ->defaultItems(1)
                            ->addActionLabel(__('Add Book'))
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make(__('Totals'))
                    ->schema([
                        TextInput::make('subtotal')
                            ->label(__('Subtotal'))
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('general_discount')
                            ->label(__('General Discount %'))
                            ->numeric()
                            ->default(0)
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            }),

                        TextInput::make('general_discount_amount')
                            ->label(__('Discount Amount'))
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('total')
                            ->label(__('Total'))
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]);
    }

    public static function updateItemNetValue(Get $get, Set $set): void
    {
        $quantity = floatval($get('quantity') ?? 0);
        $unitPrice = floatval($get('unit_price') ?? 0);
        $discount = floatval($get('discount') ?? 0);

        $gross = $quantity * $unitPrice;
        $netValue = $gross - ($gross * $discount / 100);

        $set('net_value', number_format($netValue, 2, '.', ''));

        // Update parent totals
        $items = $get('../../items') ?? [];
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += floatval($item['net_value'] ?? 0);
        }

        $generalDiscount = floatval($get('../../general_discount') ?? 0);
        $discountAmount = $subtotal * $generalDiscount / 100;
        $total = $subtotal - $discountAmount;

        $set('../../subtotal', number_format($subtotal, 2, '.', ''));
        $set('../../general_discount_amount', number_format($discountAmount, 2, '.', ''));
        $set('../../total', number_format($total, 2, '.', ''));
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $items = $get('items') ?? [];
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += floatval($item['net_value'] ?? 0);
        }

        $generalDiscount = floatval($get('general_discount') ?? 0);
        $discountAmount = $subtotal * $generalDiscount / 100;
        $total = $subtotal - $discountAmount;

        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('general_discount_amount', number_format($discountAmount, 2, '.', ''));
        $set('total', number_format($total, 2, '.', ''));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('quotation_number')
                    ->label(__('Quotation Number'))
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => QuotationResource::getUrl('view', ['record' => $record])),

                TextColumn::make('client.name')
                    ->label(__('Client'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label(__('Items'))
                    ->counts('items')
                    ->sortable(),

                TextColumn::make('subtotal')
                    ->label(__('Subtotal'))
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('general_discount')
                    ->label(__('Discount'))
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('total')
                    ->label(__('Total'))
                    ->money('EGP')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (QuotationStatus $state): string => match ($state) {
                        QuotationStatus::DRAFT => 'gray',
                        QuotationStatus::SENT => 'info',
                        QuotationStatus::APPROVED => 'success',
                        QuotationStatus::REJECTED => 'danger',
                    }),

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

                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(QuotationStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'view' => Pages\ViewQuotation::route('/{record}'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
        ];
    }
}