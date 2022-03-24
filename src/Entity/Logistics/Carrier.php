<?php

namespace App\Entity\Logistics;

use App\Entity\Embeddable\Address;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Carrier extends Entity {
    #[ORM\Embedded]
    private Address $address;

    #[
        Assert\Length(min: 3, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50)
    ]
    private ?string $name = null;

    #[Pure]
    public function __construct() {
        $this->address = new Address();
    }

    final public function getAddress(): Address {
        return $this->address;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setAddress(Address $address): self {
        $this->address = $address;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
