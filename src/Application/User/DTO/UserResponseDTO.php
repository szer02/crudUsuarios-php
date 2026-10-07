<?php

declare(strict_types=1);

namespace App\Application\User\DTO;

use App\Domain\User\User;

final readonly class UserResponseDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $createdAt
    ) {}

    public static function fromEntity(User $user): self
    {
        return new self(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail(),
            createdAt: $user->getCreatedAt()->format('d/m/Y H:i:s')
        );
    }
}