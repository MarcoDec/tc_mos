<?php

namespace App\Entity\Management;

use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Color extends Entity {
    #[
        Assert\Length(min: 3, max: 20),
        Assert\NotBlank,
        ORM\Column(length: 20)
    ]
    private ?string $name = null;

    #[
        Assert\Length(exactly: 7),
        Assert\NotBlank,
        ORM\Column(type: 'char', length: 7, options: ['charset' => 'ascii'])
    ]
    private ?string $rgb = null;

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getRgb(): ?string {
        return $this->rgb;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setRgb(?string $rgb): self {
        $this->rgb = $rgb;
        return $this;
    }
}
