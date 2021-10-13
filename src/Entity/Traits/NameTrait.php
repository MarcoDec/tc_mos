<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

trait NameTrait {
    #[
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
