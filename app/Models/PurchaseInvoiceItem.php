<?php
// app/Models/PurchaseInvoiceItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInvoiceItem extends Model
{
    protected $fillable = [
        'purchase_invoice_id',
        'book_id',
        'quantity',
        'unit_price',
        'discount',
        'net_value',
    ];

    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}