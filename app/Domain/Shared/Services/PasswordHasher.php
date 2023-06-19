<?php
declare(strict_types=1);

namespace App\Domain\Shared\Services;

use Illuminate\Support\Facades\Hash;

final class PasswordHasher
{
    public function hash(string $password): string
    {
        return Hash::make($password);
    }

    public function compare(string $passwordString, string $passwordHash): bool
    {
        return Hash::check($passwordString, $passwordHash);
    }
}
