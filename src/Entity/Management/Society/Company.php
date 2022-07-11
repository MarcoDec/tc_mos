<?php

namespace App\Entity\Management\Society;

use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Company extends Entity {
    #[
        Assert\NotBlank,
        ORM\Column(nullable: true)
    ]
    private ?string $name = null;

    #[
        Assert\NotBlank,
        ORM\ManyToOne
    ]
    private ?Society $society = null;

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getSociety(): ?Society {
        return $this->society;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setSociety(?Society $society): self {
        $this->society = $society;
        return $this;
    }
}
