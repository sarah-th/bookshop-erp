<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ClientType: string implements HasLabel
{
    case COMPANY = 'company';
    case SCHOOL = 'school';

    public function getLabel(): string
    {
        return match ($this) {
            self::COMPANY => __('Company'),
            self::SCHOOL => __('School'),
        };
    }
}