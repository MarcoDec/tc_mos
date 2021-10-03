<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait NameTrait {
    #[ORM\Column]
    private string $name;

    final public function getName(): string {
        return $this->name;
    }

    final public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }
}
