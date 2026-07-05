<?php
// app/Filament/Resources/PurchaseInvoices/Pages/EditPurchaseInvoice.php
namespace App\Filament\Resources\PurchaseInvoices\Pages;
use App\Filament\Resources\PurchaseInvoices\PurchaseInvoiceResource;
use App\Models\Book;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseInvoice extends EditRecord
{
    protected static string $resource = PurchaseInvoiceResource::class;

    protected array $itemsData = [];
    protected array $oldItems  = [];

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
        if (! PurchaseInvoiceResource::validateCurrency($data['items'] ?? [])) {
            Notification::make()
                ->title(__('Currency Mismatch'))
                ->body(__('All books must have the same currency.'))
                ->danger()
                ->send();

            $this->halt();
        }

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
        // 1. Reverse old stock increase
        foreach ($this->oldItems as $old) {
            Book::find($old['book_id'])?->decrement('current_quantity', $old['quantity']);
        }

        // 2. Replace items
        $this->record->items()->delete();
        foreach ($this->itemsData as $item) {
            $this->record->items()->create($item);
        }

        // 3. Apply new stock increase
        foreach ($this->itemsData as $item) {
            Book::find($item['book_id'])?->increment('current_quantity', $item['quantity']);
        }
    }
}