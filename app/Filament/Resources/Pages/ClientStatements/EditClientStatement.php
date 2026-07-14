<?php
// Pages/ClientStatements/EditClientStatement.php
namespace App\Filament\Resources\AccountStatements\Pages\ClientStatements;
use App\Filament\Resources\AccountStatements\ClientStatementResource;
use Filament\Resources\Pages\EditRecord;
class EditClientStatement extends EditRecord {
    protected static string $resource = ClientStatementResource::class;
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }
}