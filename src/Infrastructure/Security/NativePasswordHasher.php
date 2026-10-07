<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Application\User\Port\PasswordHasherInterface;

final readonly class NativePasswordHasher implements PasswordHasherInterface
{
    public function hash(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    public function verify(string $plainPassword, string $hashedPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword);
    }
}