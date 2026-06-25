<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BookCategory: string implements HasLabel
{
    case MATH = 'math';
    case ARABIC = 'arabic';
    case ENGLISH = 'english';
    case SCIENCE = 'science';
    case COMPUTER = 'computer';
    case SOCIAL_STUDIES = 'social_studies';
    case RELIGION = 'religion';
    case ART = 'art';
    case FRENCH = 'french';
    case PHYSICS = 'physics';
    case CHEMISTRY = 'chemistry';
    case BIOLOGY = 'biology';
    case GEOGRAPHY = 'geography';
    case HISTORY = 'history';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::MATH => __('Math'),
            self::ARABIC => __('Arabic'),
            self::ENGLISH => __('English'),
            self::SCIENCE => __('Science'),
            self::COMPUTER => __('Computer'),
            self::SOCIAL_STUDIES => __('Social Studies'),
            self::RELIGION => __('Religion'),
            self::ART => __('Art'),
            self::FRENCH => __('French'),
            self::PHYSICS => __('Physics'),
            self::CHEMISTRY => __('Chemistry'),
            self::BIOLOGY => __('Biology'),
            self::GEOGRAPHY => __('Geography'),
            self::HISTORY => __('History'),
            self::OTHER => __('Other'),
        };
    }
}