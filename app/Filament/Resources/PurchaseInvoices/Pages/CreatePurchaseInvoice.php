<?php
// app/Filament/Resources/PurchaseInvoices/Pages/CreatePurchaseInvoice.php
namespace App\Filament\Resources\PurchaseInvoices\Pages;
use App\Filament\Resources\PurchaseInvoices\PurchaseInvoiceResource;
use App\Filament\Concerns\ValidatesInvoiceCurrency;
use App\Models\PurchaseInvoice;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchaseInvoice extends CreateRecord
{
    use ValidatesInvoiceCurrency;

    protected static string $resource = PurchaseInvoiceResource::class;

    protected array $itemsData = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! PurchaseInvoiceResource::validateCurrency($data['items'] ?? [])) {
            Notification::make()
                ->title(__('Currency Mismatch'))
                ->body(__('All books must have the same currency.'))
                ->danger()
                ->send();

            $this->halt();
        }

        $this->itemsData = $data['items'] ?? [];
        unset($data['items']);
        return $data;
    }

    protected function afterCreate(): void
    {
        foreach ($this->itemsData as $item) {
            $this->record->items()->create($item);
        }

        // Increase stock on create
        $this->record->increaseStock();
    }
}