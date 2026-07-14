<?php
// Pages/ClientStatements/ListClientStatements.php
namespace App\Filament\Resources\AccountStatements\Pages\ClientStatements;

use App\Filament\Resources\AccountStatements\ClientStatementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientStatements extends ListRecords {
    protected static string $resource = ClientStatementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}