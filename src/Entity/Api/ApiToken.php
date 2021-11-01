<?php

namespace App\Entity\Api;

use App\Entity\Hr\Employee\Employee;
use App\Repository\Api\ApiTokenRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
class ApiToken {
    #[ORM\ManyToOne(targetEntity: Employee::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private Employee $employee;

    #[ORM\Column(type: 'datetime')]
    private ?DateTime $expireAt;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $token;

    public function __construct(Employee $employee) {
        $this->employee = $employee;
        $this->token = bin2hex(random_bytes(60));
        $this->expireAt = new DateTime('+1 hour');
    }

    public function expire(): void {
        $this->expireAt = new DateTime('-1 minute');
    }

    public function getEmployee(): ?Employee {
        return $this->employee;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getToken(): ?string {
        return $this->token;
    }

    public function isExpired(): bool {
        return $this->expireAt <= new DateTime();
    }

    public function renewExpiresAt(): void {
        $this->expireAt = new DateTime('+1 hour');
    }
}
