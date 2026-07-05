<?php
// app/Filament/Concerns/ValidatesInvoiceCurrency.php

namespace App\Filament\Concerns;

use App\Models\Book;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

trait ValidatesInvoiceCurrency
{
    protected function validateItemsCurrency(array $items): void
    {
        $currencyIds = collect($items)
            ->filter(fn ($item) => ! empty($item['book_id']))
            ->map(fn ($item) => Book::find($item['book_id'])?->currency_id)
            ->filter()
            ->unique();

        if ($currencyIds->count() > 1) {
            Notification::make()
                ->title(__('Currency Mismatch'))
                ->body(__('All books in an invoice must have the same currency.'))
                ->danger()
                ->send();

            $this->halt();
        }
    }
}