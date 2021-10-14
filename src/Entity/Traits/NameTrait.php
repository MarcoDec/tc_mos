<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

trait NameTrait {
    #[
        ApiProperty(description: 'Nom', required: true),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    private string $name;

    final public function getName(): string {
        return $this->name;
    }

    final public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }
}
