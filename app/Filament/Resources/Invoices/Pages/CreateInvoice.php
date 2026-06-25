<?php
namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use App\Models\Quotation;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    public function mount(): void
    {
        parent::mount();

        $quotationId = request('quotation_id');
        if (! $quotationId) return;

        $quotation = Quotation::with('items')->find($quotationId);
        if (! $quotation) return;

        $this->form->fill([
            'invoice_number'          => Invoice::generateNumber(),
            'quotation_id'            => (string) $quotation->id,
            'client_id'               => (string) $quotation->client_id,
            'date'                    => now()->toDateString(),
            'subtotal'                => $quotation->subtotal,
            'general_discount'        => $quotation->general_discount,
            'general_discount_amount' => $quotation->general_discount_amount,
            'total'                   => $quotation->total,
            'notes'                   => $quotation->notes,
            'items'                   => $quotation->items->map(fn ($item) => [
                'book_id'    => (string) $item->book_id,
                'quantity'   => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount'   => $item->discount,
                'net_value'  => $item->net_value,
            ])->toArray(),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->itemsData = $data['items'] ?? [];
        unset($data['items']);
        return $data;
    }

    protected function afterCreate(): void
    {
        foreach ($this->itemsData as $item) {
            $this->record->items()->create($item);
        }

        $this->record->decreaseStock();
    }
}