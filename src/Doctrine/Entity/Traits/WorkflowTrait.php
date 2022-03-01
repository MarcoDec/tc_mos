<?php

namespace App\Doctrine\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\Entity\Embeddable\CurrentPlace;
use Symfony\Component\Serializer\Annotation as Serializer;

trait WorkflowTrait {
    /** @var CurrentPlace */
    #[
        ApiProperty(description: 'Statut'),
        Serializer\Groups(['read:current-place'])
    ]
    protected $currentPlace;

    abstract public function __construct();

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getState(): ?string {
        return $this->currentPlace->getName();
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        /** @phpstan-ignore-next-line */
        $this->currentPlace = $currentPlace;
        return $this;
    }

    final public function setState(?string $state): self {
        $this->currentPlace->setName($state);
        return $this;
    }
}
