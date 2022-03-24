<?php

namespace App\Entity\Quality;

use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity,
    ORM\Table(name: 'quality_type')
]
class Type extends Entity {
    #[
        Assert\Length(min: 3, max: 20),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    private ?string $name = null;

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
