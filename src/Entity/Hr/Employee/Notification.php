<?php

namespace App\Entity\Hr\Employee;

use App\Entity\Entity;
use App\Repository\Hr\Employee\NotificationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification extends Entity {
    #[ORM\Column(length: 30, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: '`read`', options: ['default' => false])]
    private bool $read = false;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $subject = null;

    #[
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne
    ]
    private ?Employee $user = null;

    public function __construct() {
        $this->createdAt = new DateTimeImmutable();
    }

    final public function getCategory(): ?string {
        return $this->category;
    }

    final public function getCreatedAt(): DateTimeImmutable {
        return $this->createdAt;
    }

    final public function getSubject(): ?string {
        return $this->subject;
    }

    final public function getUser(): ?Employee {
        return $this->user;
    }

    final public function isRead(): bool {
        return $this->read;
    }

    final public function setCategory(?string $category): self {
        $this->category = $category;
        return $this;
    }

    final public function setCreatedAt(DateTimeImmutable $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }

    final public function setRead(bool $read): self {
        $this->read = $read;
        return $this;
    }

    final public function setSubject(?string $subject): self {
        $this->subject = $subject;
        return $this;
    }

    final public function setUser(?Employee $user): self {
        $this->user = $user;
        return $this;
    }
}
