<?php

namespace App\Entity\Logistics;

use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity,
    ORM\Table(name: 'incoterms')
]
class Incoterms extends Entity {
    #[
        Assert\Length(min: 3, max: 11),
        Assert\NotBlank,
        ORM\Column(length: 11),
        Serializer\Groups(['read:incoterms', 'write:incoterms'])
    ]
    private ?string $code = null;

    #[
        Assert\Length(min: 3, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    private ?string $name = null;

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
