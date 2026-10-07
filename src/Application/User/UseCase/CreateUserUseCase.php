<?php

declare(strict_types=1);

namespace App\Application\User\UseCase;

use App\Application\User\DTO\CreateUserDTO;
use App\Application\User\DTO\UserResponseDTO;
use App\Application\User\Port\PasswordHasherInterface;
use App\Application\User\Port\UserRepositoryInterface;
use App\Domain\User\User;
use DomainException;

final readonly class CreateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher
    ) {}

    public function execute(CreateUserDTO $dto): UserResponseDTO
    {
        if ($this->userRepository->findByEmail($dto->email) !== null) {
            throw new DomainException('Já existe um usuário cadastrado com este e-mail.');
        }

        $userId = bin2hex(random_bytes(16)); // Gera ID único seguro
        $passwordHash = $this->passwordHasher->hash($dto->password);

        $user = new User(
            id: $userId,
            name: $dto->name,
            email: $dto->email,
            passwordHash: $passwordHash
        );

        $this->userRepository->save($user);

        return UserResponseDTO::fromEntity($user);
    }
}