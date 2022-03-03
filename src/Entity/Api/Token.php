<?php

namespace App\Entity\Api;

use App\Entity\Hr\Employee\Employee;
use App\Repository\Api\TokenRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
class Token {
    #[
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'apiTokens')
    ]
    private Employee $employee;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $expireAt;

    #[
        ORM\Column(options: ['unsigned' => true]),
        ORM\Id,
        ORM\GeneratedValue
    ]
    private ?int $id = null;

    #[ORM\Column]
    private string $token;

    final public function __construct(Employee $employee) {
        $this->employee = $employee;
        $this->expireAt = new DateTimeImmutable('+1 hour');
        $this->token = bin2hex(random_bytes(60));
    }

    final public function expire(): self {
        $this->expireAt = new DateTimeImmutable('-1 minute');
        return $this;
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

    final public function renew(): self {
        $this->expireAt = new DateTimeImmutable('+1 hour');
        return $this;
    }
}
