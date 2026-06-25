<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case CREDIT = 'credit';

    public function getLabel(): string
    {
        return match ($this) {
            self::CASH => __('Cash'),
            self::CREDIT => __('Credit'),
        };
    }
}