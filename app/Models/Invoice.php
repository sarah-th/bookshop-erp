<?php
// app/Models/Invoice.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'quotation_id',
        'client_id',
        'date',
        'subtotal',
        'general_discount',
        'general_discount_amount',
        'total',
        'notes',
    ];

    protected $casts = [
        'date'     => 'date',
    ];

    public static function generateNumber(): string
    {
        $last = static::latest('id')->value('invoice_number');
        $next = $last ? ((int) substr($last, 4)) + 1 : 1;
        return 'INV-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Decrease stock for every item and mark invoice as confirmed.
     * Call this once when the invoice is created.
     */
    public function decreaseStock(): void
    {
        foreach ($this->items as $item) {
            $item->book->decrement('current_quantity', $item->quantity);
        }
    }
}