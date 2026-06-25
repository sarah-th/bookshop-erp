<?php
// app/Filament/Actions/CreateInvoiceAction.php

namespace App\Filament\Actions;

use App\Models\Invoice;
use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class CreateInvoiceAction
{
    public static function make(): Action
    {
        return Action::make('create_invoice')
            ->label(__('Create Invoice'))
            ->icon(Heroicon::OutlinedDocumentPlus)
            ->color('success')
            ->visible(fn ($record) => ! Invoice::where('quotation_id', $record->id)->exists())
            ->action(function ($record) {
                redirect(InvoiceResource::getUrl('create', [
                    'quotation_id' => $record->id,
                ]));
            });
    }
}