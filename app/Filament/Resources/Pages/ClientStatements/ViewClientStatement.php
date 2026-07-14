<?php
// Pages/ClientStatements/ViewClientStatement.php
namespace App\Filament\Resources\AccountStatements\Pages\ClientStatements;
use App\Filament\Resources\AccountStatements\ClientStatementResource;
use Filament\Resources\Pages\ViewRecord;
class ViewClientStatement extends ViewRecord {
    protected static string $resource = ClientStatementResource::class;
}