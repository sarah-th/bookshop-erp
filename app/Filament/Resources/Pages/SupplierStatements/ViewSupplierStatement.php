<?php
// Pages/SupplierStatements/ViewSupplierStatement.php
namespace App\Filament\Resources\AccountStatements\Pages\SupplierStatements;

use App\Filament\Resources\AccountStatements\SupplierStatementResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSupplierStatement extends ViewRecord {
    protected static string $resource = SupplierStatementResource::class;
}