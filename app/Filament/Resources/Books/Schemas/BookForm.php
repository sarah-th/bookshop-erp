<?php

namespace App\Filament\Resources\Books\Schemas;

use App\Enums\BookCategory;
use App\Enums\BookLevel;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->required(),
                Select::make('publisher_id')
                    ->relationship('publisher', 'name')
                    ->required(),
                Select::make('level')
                    ->options(BookLevel::class)
                    ->required(),
                Select::make('category')
                    ->options(BookCategory::class)
                    ->required(),
                TextInput::make('current_quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                DatePicker::make('last_purchase'),
                DatePicker::make('last_sell'),
                TextInput::make('cost')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
            ]);
    }
}
