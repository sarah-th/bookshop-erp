<?php
// app/Models/AccountStatement.php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\StatementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountStatement extends Model
{
    protected $fillable = [
        'type',
        'client_id',
        'supplier_id',
        'date',
        'amount',
        'payment_method',
        'reference',
        'notes',
        'currency_id',
    ];

    protected $casts = [
        'date'           => 'date',
        'type'           => StatementType::class,
        'payment_method' => PaymentMethod::class,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // Accessor to get the party name regardless of type
    public function getPartyNameAttribute(): string
    {
        return $this->client?->name ?? $this->supplier?->name ?? '-';
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}