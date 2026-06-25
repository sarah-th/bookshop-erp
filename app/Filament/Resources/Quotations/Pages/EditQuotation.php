<?php

namespace App\Filament\Resources\Quotations\Pages;

use App\Filament\Resources\Quotations\QuotationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        $subtotal = 0;

        foreach ($record->items as $item) {
            $quantity = floatval($item->quantity);
            $unitPrice = floatval($item->unit_price);
            $discount = floatval($item->discount);

            $gross = $quantity * $unitPrice;
            $netValue = $gross - ($gross * $discount / 100);

            $item->update(['net_value' => $netValue]);
            $subtotal += $netValue;
        }

        $generalDiscount = floatval($record->general_discount);
        $discountAmount = $subtotal * $generalDiscount / 100;

        $record->update([
            'subtotal' => $subtotal,
            'general_discount_amount' => $discountAmount,
            'total' => $subtotal - $discountAmount,
        ]);
    }
}
