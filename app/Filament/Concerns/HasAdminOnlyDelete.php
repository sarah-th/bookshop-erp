<?php
// app/Filament/Concerns/HasAdminOnlyDelete.php

namespace App\Filament\Concerns;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Support\FilamentAuth;

trait HasAdminOnlyDelete
{
    public static function canDelete(Model $record): bool
    {
        return FilamentAuth::isAdmin();
    }

    public static function canDeleteAny(): bool
    {
        return FilamentAuth::isAdmin();
    }
}