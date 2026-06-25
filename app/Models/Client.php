<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\PaymentMethod;
use App\Enums\ClientType;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'type',
        'phone',
        'address',
        'email',
        'payment_method',
    ];

    protected $casts = [
        'type' => ClientType::class,
        'payment_method' => PaymentMethod::class,
    ];

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }
}
