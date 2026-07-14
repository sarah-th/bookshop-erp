<?php
// Pages/SupplierStatements/CreateSupplierStatement.php
namespace App\Filament\Resources\AccountStatements\Pages\SupplierStatements;

use App\Filament\Resources\AccountStatements\SupplierStatementResource;
use Filament\Resources\Pages\CreateRecord;
class CreateSupplierStatement extends CreateRecord {
    protected static string $resource = SupplierStatementResource::class;
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }
}