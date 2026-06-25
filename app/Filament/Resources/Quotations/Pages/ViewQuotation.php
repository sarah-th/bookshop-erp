<?php

namespace App\Filament\Resources\Quotations\Pages;


use App\Enums\QuotationStatus;
use App\Filament\Resources\Quotations\QuotationResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use App\Filament\Actions\CreateInvoiceAction;

class ViewQuotation extends ViewRecord
{
    protected static string $resource = QuotationResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('Quotation Information'))
                    ->schema([
                        TextEntry::make('quotation_number')
                            ->label(__('Quotation Number'))
                            ->weight(FontWeight::Bold),

                        TextEntry::make('client.name')
                            ->label(__('Client')),

                        TextEntry::make('date')
                            ->label(__('Date'))
                            ->date('d/m/Y'),

                        TextEntry::make('valid_until')
                            ->label(__('Valid Until'))
                            ->date('d/m/Y')
                            ->placeholder('-'),

                        TextEntry::make('status')
                            ->label(__('Status'))
                            ->badge()
                            ->color(fn (QuotationStatus $state): string => match ($state) {
                                QuotationStatus::DRAFT     => 'gray',
                                QuotationStatus::SENT      => 'info',
                                QuotationStatus::APPROVED  => 'success',
                                QuotationStatus::REJECTED  => 'danger',
                            }),

                        TextEntry::make('notes')
                            ->label(__('Notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make(__('Quotation Items'))
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
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
                                    ->money('EGP')
                                    ->columnSpan(2),

                                TextEntry::make('discount')
                                    ->label(__('Discount %'))
                                    ->suffix('%')
                                    ->columnSpan(2),

                                TextEntry::make('net_value')
                                    ->label(__('Net Value'))
                                    ->money('EGP')
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan(2),
                            ])
                            ->columns(10)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make(__('Totals'))
                    ->schema([
                        TextEntry::make('subtotal')
                            ->label(__('Subtotal'))
                            ->money('EGP'),

                        TextEntry::make('general_discount')
                            ->label(__('General Discount %'))
                            ->suffix('%'),

                        TextEntry::make('general_discount_amount')
                            ->label(__('Discount Amount'))
                            ->money('EGP'),

                        TextEntry::make('total')
                            ->label(__('Total'))
                            ->money('EGP')
                            ->weight(FontWeight::Bold),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            CreateInvoiceAction::make(),
            Actions\Action::make('download_pdf')
                ->label(__('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return $this->downloadPdf();
                }),
            Actions\Action::make('print')
                ->label(__('Print'))
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn () => route('quotation.print', $this->record))
                ->openUrlInNewTab(),
        ];
    }

    protected function downloadPdf()
    {
        $quotation = $this->record;
        $pdf = Pdf::loadView('pdf.quotation', compact('quotation'));
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'quotation-' . $quotation->quotation_number . '.pdf');
    }
}