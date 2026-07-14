<?php
// Pages/ClientStatements/CreateClientStatement.php
namespace App\Filament\Resources\AccountStatements\Pages\ClientStatements;

use App\Filament\Resources\AccountStatements\ClientStatementResource;
use Filament\Resources\Pages\CreateRecord;
class CreateClientStatement extends CreateRecord {
    protected static string $resource = ClientStatementResource::class;
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }
}