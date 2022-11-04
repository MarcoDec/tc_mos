<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Hr\Employee\Employee;
use App\Repository\TokenRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
class Token extends Entity {
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $expireAt;

    #[ORM\Column(type: 'char', length: 120)]
    private string $token;

    public function __construct(
        #[ORM\JoinColumn(nullable: false),
            ORM\ManyToOne(inversedBy: 'apiTokens')]
        private Employee $employee
    ) {
        $this->employee->addApiToken($this);
        $this->expireAt = new DateTimeImmutable('+1 hour');
        $this->token = bin2hex(random_bytes(60));
    }

    public function getEmployee(): Employee {
        return $this->employee;
    }

    public function getExpireAt(): DateTimeImmutable {
        return $this->expireAt;
    }

    public function getToken(): string {
        return $this->token;
    }
}
