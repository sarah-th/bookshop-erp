<?php

namespace App\Models;

use App\Enums\BookCategory;
use App\Enums\BookLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'name',
        'isbn',
        'supplier_id',
        'publisher_id',
        'level',
        'category',
        'current_quantity',
        'last_purchase',
        'last_sell',
        'cost',
        'currency_id',
    ];

    protected $casts = [
        'level' => BookLevel::class,
        'category' => BookCategory::class,
        'last_purchase' => 'date',
        'last_sell' => 'date',
        'cost' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function quotationItems(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}