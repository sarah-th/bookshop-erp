<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\QuotationStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    protected $fillable = [
        'quotation_number',
        'client_id',
        'date',
        'valid_until',
        'subtotal',
        'general_discount',
        'general_discount_amount',
        'total',
        'notes',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'general_discount' => 'decimal:2',
        'general_discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'status' => QuotationStatus::class,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public static function generateNumber(): string
    {
        $latest = static::latest('id')->first();
        $number = $latest ? intval(substr($latest->quotation_number, 4)) + 1 : 1;
        return 'QUO-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
