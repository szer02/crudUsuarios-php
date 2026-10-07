<?php

declare(strict_types=1);

namespace App\Application\User\UseCase;

use App\Application\User\DTO\UserResponseDTO;
use App\Application\User\Port\UserRepositoryInterface;
use DomainException;

final readonly class GetUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(string $id): UserResponseDTO
    {
        $user = $this->userRepository->findById($id);

        if ($user === null) {
            throw new DomainException('Usuário não encontrado.');
        }

        return UserResponseDTO::fromEntity($user);
    }
}