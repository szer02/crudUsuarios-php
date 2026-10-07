<?php

declare(strict_types=1);

namespace App\Application\User\UseCase;

use App\Application\User\DTO\UserResponseDTO;
use App\Application\User\Port\UserRepositoryInterface;

final readonly class ListUsersUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * @return UserResponseDTO[]
     */
    public function execute(): array
    {
        $users = $this->userRepository->findAll();

        return array_map(
            fn($user) => UserResponseDTO::fromEntity($user),
            $users
        );
    }
}
