<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuotationStatus: string implements HasLabel
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => __('Draft'),
            self::SENT => __('Sent'),
            self::APPROVED => __('Approved'),
            self::REJECTED => __('Rejected'),
        };
    }
}