<?php
// app/Filament/Support/FilamentAuth.php

namespace App\Filament\Support;

use App\Enums\UserRole;

class FilamentAuth
{
    public static function isAdmin(): bool
    {
        return auth()->user()?->role === UserRole::ADMIN;
    }
}