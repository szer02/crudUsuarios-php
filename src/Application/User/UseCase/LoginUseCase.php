<?php

declare(strict_types=1);

namespace App\Application\User\UseCase;

use App\Application\User\DTO\LoginDTO;
use App\Application\User\DTO\UserResponseDTO;
use App\Application\User\Port\PasswordHasherInterface;
use App\Application\User\Port\UserRepositoryInterface;

final readonly class LoginUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher
    ) {}

    public function execute(LoginDTO $dto): ?UserResponseDTO
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if ($user === null) {
            return null;
        }

        if (!$this->passwordHasher->verify($dto->password, $user->getPasswordHash())) {
            return null;
        }

        return new UserResponseDTO(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail(),
            createdAt: $user->getCreatedAt()->format('d/m/Y H:i:s')
        );
    }
}