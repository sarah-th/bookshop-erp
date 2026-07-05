<?php
// app/Models/PurchaseInvoice.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseInvoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'supplier_id',
        'date',
        'due_date',
        'subtotal',
        'general_discount',
        'general_discount_amount',
        'total',
        'notes',
    ];

    protected $casts = [
        'date'     => 'date',
        'due_date' => 'date',
    ];

    public static function generateNumber(): string
    {
        $last = static::latest('id')->value('invoice_number');
        $next = $last ? ((int) substr($last, 4)) + 1 : 1;
        return 'PIN-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    public function increaseStock(): void
    {
        foreach ($this->items as $item) {
            $item->book->increment('current_quantity', $item->quantity);
        }
    }

    protected static function booted(): void
    {
        // Restore stock when purchase invoice is deleted
        static::deleting(function (PurchaseInvoice $invoice) {
            foreach ($invoice->items as $item) {
                $item->book->decrement('current_quantity', $item->quantity);
            }
        });
    }
}