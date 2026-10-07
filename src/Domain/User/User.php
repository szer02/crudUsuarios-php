<?php

declare(strict_types=1);

namespace App\Domain\User;

use DateTimeImmutable;
use InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string', length: 36, unique: true)]
        private readonly string $id,

        #[ORM\Column(type: 'string', length: 255)]
        private string $name,

        #[ORM\Column(type: 'string', length: 255, unique: true)]
        private string $email,

        #[ORM\Column(type: 'string', length: 255)]
        private string $passwordHash,

        #[ORM\Column(type: 'datetime_immutable')]
        private readonly DateTimeImmutable $createdAt = new DateTimeImmutable()
    ) {
        $this->setName($name);
        $this->setEmail($email);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updateProfile(string $name, string $email): void
    {
        $this->setName($name);
        $this->setEmail($email);
    }

    public function changePassword(string $newPasswordHash): void
    {
        if (trim($newPasswordHash) === '') {
            throw new InvalidArgumentException('O hash da palavra-passe não pode ser vazio.');
        }

        $this->passwordHash = $newPasswordHash;
    }

    private function setName(string $name): void
    {
        $trimmedName = trim($name);
        if (strlen($trimmedName) < 2) {
            throw new InvalidArgumentException('O nome deve possuir pelo menos 2 caracteres.');
        }

        $this->name = $trimmedName;
    }

    private function setEmail(string $email): void
    {
        $trimmedEmail = trim($email);
        if (!filter_var($trimmedEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Formato de e-mail inválido.');
        }

        $this->email = $trimmedEmail;
    }
}