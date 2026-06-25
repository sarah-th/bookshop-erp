<?php

namespace App\Filament\Resources\Quotations\Pages;

use App\Filament\Resources\Quotations\QuotationResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $subtotal = 0;

        $generalDiscount = floatval($data['general_discount'] ?? 0);
        $discountAmount = $subtotal * $generalDiscount / 100;

        $data['subtotal'] = $subtotal;
        $data['general_discount_amount'] = $discountAmount;
        $data['total'] = $subtotal - $discountAmount;

        return $data;
    }

    protected function afterCreate(): void
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