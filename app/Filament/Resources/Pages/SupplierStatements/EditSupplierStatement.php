<?php
// Pages/SupplierStatements/EditSupplierStatement.php
namespace App\Filament\Resources\AccountStatements\Pages\SupplierStatements;

use App\Filament\Resources\AccountStatements\SupplierStatementResource;
use Filament\Resources\Pages\EditRecord;
class EditSupplierStatement extends EditRecord {
    protected static string $resource = SupplierStatementResource::class;
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }
}