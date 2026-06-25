<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BookLevel: string implements HasLabel
{
    case KG = 'kg';
    case PLAY = 'play';
    case GRADE_1 = 'grade_1';
    case GRADE_2 = 'grade_2';
    case GRADE_3 = 'grade_3';
    case GRADE_4 = 'grade_4';
    case GRADE_5 = 'grade_5';
    case GRADE_6 = 'grade_6';
    case GRADE_7 = 'grade_7';
    case GRADE_8 = 'grade_8';
    case GRADE_9 = 'grade_9';
    case GRADE_10 = 'grade_10';
    case GRADE_11 = 'grade_11';
    case GRADE_12 = 'grade_12';
    case PRIMARY = 'primary';
    case PREP = 'prep';

    public function getLabel(): string
    {
        return match ($this) {
            self::KG => __('KG'),
            self::PLAY => __('Play'),
            self::GRADE_1 => __('Grade 1'),
            self::GRADE_2 => __('Grade 2'),
            self::GRADE_3 => __('Grade 3'),
            self::GRADE_4 => __('Grade 4'),
            self::GRADE_5 => __('Grade 5'),
            self::GRADE_6 => __('Grade 6'),
            self::GRADE_7 => __('Grade 7'),
            self::GRADE_8 => __('Grade 8'),
            self::GRADE_9 => __('Grade 9'),
            self::GRADE_10 => __('Grade 10'),
            self::GRADE_11 => __('Grade 11'),
            self::GRADE_12 => __('Grade 12'),
            self::PRIMARY => __('Primary'),
            self::PREP => __('Prep (Senior)'),
        };
    }
}