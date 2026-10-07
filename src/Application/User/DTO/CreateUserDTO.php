<?php

declare(strict_types=1);

namespace App\Application\User\DTO;

final readonly class CreateUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}
}