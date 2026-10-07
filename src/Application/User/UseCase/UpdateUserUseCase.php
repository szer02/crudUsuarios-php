<?php

declare(strict_types=1);

namespace App\Application\User\UseCase;

use App\Application\User\DTO\UpdateUserDTO;
use App\Application\User\DTO\UserResponseDTO;
use App\Application\User\Port\UserRepositoryInterface;
use DomainException;

final readonly class UpdateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(UpdateUserDTO $dto): UserResponseDTO
    {
        $user = $this->userRepository->findById($dto->id);

        if ($user === null) {
            throw new DomainException('Usuário não encontrado.');
        }

        // Verifica se o novo e-mail já pertence a outro usuário
        $existingUserWithEmail = $this->userRepository->findByEmail($dto->email);
        if ($existingUserWithEmail !== null && $existingUserWithEmail->getId() !== $dto->id) {
            throw new DomainException('Este e-mail já está sendo utilizado por outro usuário.');
        }

        $user->updateProfile($dto->name, $dto->email);
        $this->userRepository->save($user);

        return UserResponseDTO::fromEntity($user);
    }
}