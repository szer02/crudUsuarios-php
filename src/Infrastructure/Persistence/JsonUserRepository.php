<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Application\User\Port\UserRepositoryInterface;
use App\Domain\User\User;
use DateTimeImmutable;

final class JsonUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly string $filePath
    ) {
        $directory = dirname($this->filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        $rows = $this->loadData();
        return array_map(fn(array $row) => $this->hydrate($row), $rows);
    }

    public function findById(string $id): ?User
    {
        $rows = $this->loadData();
        foreach ($rows as $row) {
            if ($row['id'] === $id) {
                return $this->hydrate($row);
            }
        }

        return null;
    }

    public function findByEmail(string $email): ?User
    {
        $rows = $this->loadData();
        $normalizedEmail = strtolower(trim($email));

        foreach ($rows as $row) {
            if (strtolower($row['email']) === $normalizedEmail) {
                return $this->hydrate($row);
            }
        }

        return null;
    }

    public function save(User $user): void
    {
        $rows = $this->loadData();
        $userFound = false;

        $serialized = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'passwordHash' => $user->getPasswordHash(),
            'createdAt' => $user->getCreatedAt()->format(DateTimeImmutable::ATOM),
        ];

        foreach ($rows as $index => $row) {
            if ($row['id'] === $user->getId()) {
                $rows[$index] = $serialized;
                $userFound = true;
                break;
            }
        }

        if (!$userFound) {
            $rows[] = $serialized;
        }

        $this->saveData($rows);
    }

    public function delete(string $id): void
    {
        $rows = $this->loadData();
        $filtered = array_values(array_filter($rows, fn(array $row) => $row['id'] !== $id));

        $this->saveData($filtered);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadData(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $content = file_get_contents($this->filePath);
        if ($content === false || trim($content) === '') {
            return [];
        }

        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    /**
     * @param array<int, array<string, mixed>> $data
     */
    private function saveData(array $data): void
    {
        file_put_contents(
            $this->filePath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX // Garante bloqueio exclusivo para evitar escrita simultânea concorrente
        );
    }

    private function hydrate(array $data): User
    {
        return new User(
            id: (string) $data['id'],
            name: (string) $data['name'],
            email: (string) $data['email'],
            passwordHash: (string) $data['passwordHash'],
            createdAt: new DateTimeImmutable($data['createdAt'])
        );
    }
}