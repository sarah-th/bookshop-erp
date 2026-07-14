<?php
// app/Enums/StatementType.php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StatementType: string implements HasLabel
{
    case CLIENT   = 'client';
    case SUPPLIER = 'supplier';

    public function getLabel(): string
    {
        return match ($this) {
            self::CLIENT   => __('Client'),
            self::SUPPLIER => __('Supplier'),
        };
    }
}