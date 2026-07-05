<?php
namespace App\Filament\Resources\PurchaseInvoices;

use App\Filament\Resources\PurchaseInvoices\Pages;
use App\Filament\Support\FilamentAuth;
use App\Models\Book;
use App\Models\PurchaseInvoice;
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
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PurchaseInvoiceResource extends Resource
{
    protected static ?string $model = PurchaseInvoice::class;
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowDown;

    public static function getModelLabel(): string       { return __('Purchase Invoice'); }
    public static function getPluralModelLabel(): string  { return __('Purchase Invoices'); }
    public static function getNavigationLabel(): string   { return __('Purchase Invoices'); }
    public static function getNavigationGroup(): string   { return __('Purchases'); }

    public static function getCurrencySymbol(Get $get): ?string
    {
        $items = $get('items') ?? [];
        $firstItem = collect($items)->firstWhere(fn ($item) => ! empty($item['book_id']));
        if (! $firstItem) return null;

        $book = Book::with('currency')->find($firstItem['book_id']);
        return $book?->currency?->symbol ?? $book?->currency?->code ?? null;
    }

    public static function validateCurrency(array $items): bool
    {
        $currencyIds = collect($items)
            ->filter(fn ($item) => ! empty($item['book_id']))
            ->map(fn ($item) => Book::find($item['book_id'])?->currency_id)
            ->filter()
            ->unique();

        return $currencyIds->count() <= 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('Purchase Invoice Information'))
                    ->schema([
                        TextInput::make('invoice_number')
                            ->label(__('Invoice Number'))
                            ->default(fn () => PurchaseInvoice::generateNumber())
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(),

                        Select::make('supplier_id')
                            ->label(__('Supplier'))
                            ->options(fn () => \App\Models\Supplier::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        DatePicker::make('date')
                            ->label(__('Date'))
                            ->default(now())
                            ->required()
                            ->native(false),

                        Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make(__('Purchase Invoice Items'))
                    ->schema([
                        Repeater::make('items')
                            ->label('')
                            ->minItems(1)
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotals($get, $set))
                            ->schema([
                                Select::make('book_id')
                                    ->label(__('Book'))
                                    ->options(fn () => Book::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->hint(fn ($state) => $state
                                        ? 'Stock: ' . (Book::find($state)?->current_quantity ?? 0)
                                        : null)
                                    ->hintColor('info')
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        if (! $state) return;

                                        $book = Book::find($state);

                                        // Validate same currency across all items
                                        $items = $get('../../items') ?? [];
                                        $existingCurrencyIds = collect($items)
                                            ->filter(fn ($item) => ! empty($item['book_id'])
                                                && (string) $item['book_id'] !== (string) $state)
                                            ->map(fn ($item) => Book::find($item['book_id'])?->currency_id)
                                            ->filter()
                                            ->unique();

                                        if (
                                            $existingCurrencyIds->count() > 0 &&
                                            ! $existingCurrencyIds->contains($book?->currency_id)
                                        ) {
                                            $set('book_id', null);
                                            $set('unit_price', 0);
                                            $set('net_value', 0);

                                            Notification::make()
                                                ->title(__('Currency Mismatch'))
                                                ->body(__('All books in a purchase invoice must have the same currency.'))
                                                ->danger()
                                                ->send();

                                            return;
                                        }

                                        $set('unit_price', $book?->cost ?? 0);
                                        self::updateItemNetValue($get, $set);
                                    })
                                    ->columnSpan(3),

                                TextInput::make('quantity')
                                    ->label(__('Quantity'))
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateItemNetValue($get, $set))
                                    ->columnSpan(1),

                                TextInput::make('unit_price')
                                    ->label(__('Unit Price'))
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateItemNetValue($get, $set))
                                    ->columnSpan(2),

                                TextInput::make('discount')
                                    ->label(__('Discount %'))
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateItemNetValue($get, $set))
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
                        TextEntry::make('currency')
                            ->label(__('Currency'))
                            ->state(function (Get $get) {
                                $items = $get('items') ?? [];
                                $firstItem = collect($items)->firstWhere(fn ($i) => ! empty($i['book_id']));
                                if (! $firstItem) return '-';

                                $book = Book::with('currency')->find($firstItem['book_id']);
                                $currency = $book?->currency;

                                return $currency
                                    ? "{$currency->name} ({$currency->code})"
                                    : '-';
                            })
                            ->live(),

                        TextInput::make('subtotal')
                            ->label(__('Subtotal'))
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->prefix(fn (Get $get) => self::getCurrencySymbol($get)),

                        TextInput::make('general_discount')
                            ->label(__('General Discount %'))
                            ->numeric()
                            ->default(0)
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100)
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotals($get, $set)),

                        TextInput::make('general_discount_amount')
                            ->label(__('Discount Amount'))
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->prefix(fn (Get $get) => self::getCurrencySymbol($get)),

                        TextInput::make('total')
                            ->label(__('Total'))
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->prefix(fn (Get $get) => self::getCurrencySymbol($get)),
                    ])
                    ->columns(5)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(PurchaseInvoice::query()->with('items.book.currency'))
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),

                TextColumn::make('invoice_number')
                    ->label(__('Invoice Number'))
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => PurchaseInvoiceResource::getUrl('view', ['record' => $record])),

                TextColumn::make('supplier.name')
                    ->label(__('Supplier'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label(__('Due Date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('total')
                    ->label(__('Total'))
                    ->sortable()
                    ->weight('bold')
                    ->formatStateUsing(function ($state, $record) {
                        $currency = $record->items->first()?->book?->currency;
                        $symbol = $currency?->symbol ?? $currency?->code ?? 'EGP';
                        return $symbol . ' ' . number_format($state, 2);
                    }),
            ])
            ->filters([
                SelectFilter::make('supplier_id')
                    ->label(__('Supplier'))
                    ->relationship('supplier', 'name'),
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
            ->defaultSort('id', 'desc');
    }

    public static function updateItemNetValue(Get $get, Set $set): void
    {
        $quantity  = floatval($get('quantity') ?? 0);
        $unitPrice = floatval($get('unit_price') ?? 0);
        $discount  = floatval($get('discount') ?? 0);

        $gross    = $quantity * $unitPrice;
        $netValue = $gross - ($gross * $discount / 100);
        $set('net_value', number_format($netValue, 2, '.', ''));

        $items    = $get('../../items') ?? [];
        $subtotal = collect($items)->sum(fn ($i) => floatval($i['net_value'] ?? 0));

        $generalDiscount = floatval($get('../../general_discount') ?? 0);
        $discountAmount  = $subtotal * $generalDiscount / 100;

        $set('../../subtotal',                number_format($subtotal, 2, '.', ''));
        $set('../../general_discount_amount', number_format($discountAmount, 2, '.', ''));
        $set('../../total',                   number_format($subtotal - $discountAmount, 2, '.', ''));
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $items    = $get('items') ?? [];
        $subtotal = collect($items)->sum(fn ($i) => floatval($i['net_value'] ?? 0));

        $generalDiscount = floatval($get('general_discount') ?? 0);
        $discountAmount  = $subtotal * $generalDiscount / 100;

        $set('subtotal',                number_format($subtotal, 2, '.', ''));
        $set('general_discount_amount', number_format($discountAmount, 2, '.', ''));
        $set('total',                   number_format($subtotal - $discountAmount, 2, '.', ''));
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPurchaseInvoices::route('/'),
            'create' => Pages\CreatePurchaseInvoice::route('/create'),
            'view'   => Pages\ViewPurchaseInvoice::route('/{record}'),
            'edit'   => Pages\EditPurchaseInvoice::route('/{record}/edit'),
        ];
    }
}