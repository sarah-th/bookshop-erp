<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use App\Filament\Support\FilamentAuth;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('Invoice Information'))
                    ->schema([
                        TextEntry::make('invoice_number')
                            ->label(__('Invoice Number'))
                            ->weight(FontWeight::Bold),

                        TextEntry::make('client.name')
                            ->label(__('Client')),

                        TextEntry::make('quotation.quotation_number')
                            ->label(__('From Quotation'))
                            ->placeholder('-'),

                        TextEntry::make('date')
                            ->label(__('Date'))
                            ->date('d/m/Y'),

                        TextEntry::make('notes')
                            ->label(__('Notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make(__('Invoice Items'))
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('book.publisher.name')
                                    ->label(__('Publisher'))
                                    ->placeholder('-')
                                    ->columnSpan(2),

                                TextEntry::make('book.name')
                                    ->label(__('Book'))
                                    ->columnSpan(3),

                                TextEntry::make('book.isbn')
                                    ->label(__('ISBN'))
                                    ->columnSpan(2),

                                TextEntry::make('quantity')
                                    ->label(__('Quantity'))
                                    ->columnSpan(1),

                                TextEntry::make('unit_price')
                                    ->label(__('Unit Price'))
                                    ->numeric(decimalPlaces: 2)
                                    ->columnSpan(2),

                                TextEntry::make('discount')
                                    ->label(__('Discount %'))
                                    ->suffix('%')
                                    ->columnSpan(1),

                                TextEntry::make('net_value')
                                    ->label(__('Net Value'))
                                    ->numeric(decimalPlaces: 2)
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan(2),
                            ])
                            ->columns(13)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make(__('Totals'))
                    ->schema([
                        TextEntry::make('currency')
                            ->label(__('Currency'))
                            ->state(function ($record) {
                                $book = $record->items->first()?->book?->load('currency');
                                $currency = $book?->currency;
                                return $currency
                                    ? "{$currency->name} ({$currency->code})"
                                    : '-';
                            }),

                        TextEntry::make('subtotal')
                            ->label(__('Subtotal'))
                            ->numeric(decimalPlaces: 2),

                        TextEntry::make('general_discount')
                            ->label(__('General Discount %'))
                            ->suffix('%'),

                        TextEntry::make('general_discount_amount')
                            ->label(__('Discount Amount'))
                            ->numeric(decimalPlaces: 2),

                        TextEntry::make('total')
                            ->label(__('Total'))
                            ->numeric(decimalPlaces: 2)
                            ->weight(FontWeight::Bold),
                    ])
                    ->columns(5)
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()->visible(fn () => FilamentAuth::isAdmin()),
            Actions\Action::make('download_pdf')
                ->label(__('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn () => $this->downloadPdf()),
            Actions\Action::make('print')
                ->label(__('Print'))
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn () => route('invoice.print', $this->record))
                ->openUrlInNewTab(),
        ];
    }

    protected function downloadPdf()
    {
        $invoice = $this->record->load('client', 'items.book.publisher', 'items.book.currency', 'quotation');
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'invoice-' . $invoice->invoice_number . '.pdf');
    }
}
