<?php

namespace App\Entity\Hr;

use App\Entity\Embeddable\Address;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class OutTrainer extends Entity {
    #[
        ORM\Embedded,
        Serializer\Groups(['read:out-trainer', 'write:out-trainer'])
    ]
    private Address $address;

    #[
        Assert\Length(min: 3, max: 30),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    private ?string $name = null;

    #[
        Assert\Length(min: 3, max: 30),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(['read:out-trainer', 'write:out-trainer'])
    ]
    private ?string $surname = null;

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

    final public function getSurname(): ?string {
        return $this->surname;
    }

    final public function setAddress(Address $address): self {
        $this->address = $address;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setSurname(?string $surname): self {
        $this->surname = $surname;
        return $this;
    }
}
