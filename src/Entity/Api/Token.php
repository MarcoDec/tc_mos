<?php

namespace App\Entity\Api;

use App\Entity\Hr\Employee\Employee;
use App\Repository\Api\TokenRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
class Token {
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $expireAt;

    #[
        ORM\Column(options: ['unsigned' => true]),
        ORM\Id,
        ORM\GeneratedValue
    ]
    private ?int $id = null;

    #[ORM\Column(type: 'char', length: 120)]
    private string $token;

    public function __construct(
        #[ORM\JoinColumn(nullable: false),
        ORM\ManyToOne(inversedBy: 'apiTokens')]
        private Employee $employee
    ) {
        $this->expireAt = new DateTimeImmutable('+4 hour');
        $this->token = bin2hex(random_bytes(60));
    }

    final public function getEmployee(): ?Employee {
        return $this->employee;
    }

    final public function getId(): ?int {
        return $this->id;
    }

    final public function getToken(): string {
        return $this->token;
    }

    #[Pure]
    final public function getUserIdentifier(): string {
        return $this->employee->getUserIdentifier();
    }

    final public function isExpired(): bool {
        return $this->expireAt <= new DateTimeImmutable();
    }
}
