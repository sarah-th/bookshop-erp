<?php
// app/Enums/PaymentMethod.php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel, HasColor
{
    case CASH          = 'cash';
    case CREDIT        = 'credit';
    case BANK_TRANSFER = 'bank_transfer';
    case CHEQUE        = 'cheque';

    public function getLabel(): string
    {
        return match ($this) {
            self::CASH          => __('Cash'),
            self::CREDIT        => __('Credit'),
            self::BANK_TRANSFER => __('Bank Transfer'),
            self::CHEQUE        => __('Cheque'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::CASH          => 'success',
            self::CREDIT        => 'warning',
            self::BANK_TRANSFER => 'info',
            self::CHEQUE        => 'gray',
        };
    }
}