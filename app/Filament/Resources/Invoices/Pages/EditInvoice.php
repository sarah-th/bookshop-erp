<?php
namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Book;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected array $itemsData = [];
    protected array $oldItems = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['items'] = $this->record->items->map(fn ($item) => [
            'book_id'    => (string) $item->book_id,
            'quantity'   => $item->quantity,
            'unit_price' => $item->unit_price,
            'discount'   => $item->discount,
            'net_value'  => $item->net_value,
        ])->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Snapshot old items before we overwrite them
        $this->oldItems = $this->record->items->map(fn ($item) => [
            'book_id'  => $item->book_id,
            'quantity' => $item->quantity,
        ])->toArray();

        $this->itemsData = $data['items'] ?? [];
        unset($data['items']);

        return $data;
    }

    protected function afterSave(): void
    {
        // 1. Restore stock from old items
        foreach ($this->oldItems as $old) {
            Book::find($old['book_id'])?->increment('current_quantity', $old['quantity']);
        }

        // 2. Delete old items and insert new ones
        $this->record->items()->delete();

        foreach ($this->itemsData as $item) {
            $this->record->items()->create($item);
        }

        // 3. Decrease stock with new quantities
        foreach ($this->itemsData as $item) {
            Book::find($item['book_id'])?->decrement('current_quantity', $item['quantity']);
        }
    }
}