<?php

declare(strict_types=1);

namespace App\Application\User\Port;

use App\Domain\User\User;

interface UserRepositoryInterface
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    public function findById(string $id): ?User;

    public function findByEmail(string $email): ?User;

    public function save(User $user): void;

    public function delete(string $id): void;
}