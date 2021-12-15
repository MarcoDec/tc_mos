<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

trait RefTrait {
   #[
        ApiProperty(description: 'Référence'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:ref', 'write:ref'])
    ]
    protected ?string $ref = null;

    final function getRef(): ?string {
        return $this->ref;
    }

    final function setRef(?string $ref): self {
        $this->ref = $ref;
        return $this;
    }

    final function __toString(): string {
        return $this->getRef() ?? parent::__toString();
    }

}
