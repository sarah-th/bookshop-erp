<?php
// Pages/SupplierStatements/ListSupplierStatements.php
namespace App\Filament\Resources\AccountStatements\Pages\SupplierStatements;

use App\Filament\Resources\AccountStatements\SupplierStatementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSupplierStatements extends ListRecords {
    protected static string $resource = SupplierStatementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}