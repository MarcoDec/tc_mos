<?php

namespace App\Entity\It;

use App\Entity\Entity;
use App\Entity\Hr\Employee\Employee;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Request extends Entity {
    #[ORM\Column(type: 'date_immutable')]
    private ?DateTimeImmutable $askedAt = null;

    #[ORM\ManyToOne]
    private ?Employee $askedBy = null;

    #[ORM\Column]
    private ?string $currentPlace = null;

    #[ORM\Column(type: 'date_immutable')]
    private ?DateTimeImmutable $delay = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column]
    private ?string $name = null;

    #[ORM\Column]
    private ?string $version = null;

    final public function getAskedAt(): ?DateTimeImmutable {
        return $this->askedAt;
    }

    final public function getAskedBy(): ?Employee {
        return $this->askedBy;
    }

    final public function getCurrentPlace(): ?string {
        return $this->currentPlace;
    }

    final public function getDelay(): ?DateTimeImmutable {
        return $this->delay;
    }

    final public function getDescription(): ?string {
        return $this->description;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getVersion(): ?string {
        return $this->version;
    }

    final public function setAskedAt(?DateTimeImmutable $askedAt): self {
        $this->askedAt = $askedAt;
        return $this;
    }

    final public function setAskedBy(?Employee $askedBy): self {
        $this->askedBy = $askedBy;
        return $this;
    }

    final public function setCurrentPlace(?string $currentPlace): self {
        $this->currentPlace = $currentPlace;
        return $this;
    }

    final public function setDelay(?DateTimeImmutable $delay): self {
        $this->delay = $delay;
        return $this;
    }

    final public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setVersion(?string $version): self {
        $this->version = $version;
        return $this;
    }
}
