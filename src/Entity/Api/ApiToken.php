<?php

namespace App\Entity\Api;

use App\Entity\Hr\Employee\Employee;
use App\Repository\Api\ApiTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
class ApiToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $token;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $expireAt;

    #[ORM\ManyToOne(targetEntity: Employee::class, fetch:"EAGER" )]
    #[ORM\JoinColumn(nullable: false)]
    private Employee $employee;

    public function __construct(Employee $employee) {
       $this->employee = $employee;
       $this->token = bin2hex(random_bytes(60));
       $this->expireAt = new \DateTime('+1 hour');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function renewExpiresAt(){
      $this->expireAt = new \DateTime('+1 hour');
    }

   public function expire() {
      $this->expireAt = new \DateTime('-1 minute');
   }
   public function isExpired():bool {
      return $this->expireAt <= new \DateTime();
   }
}
