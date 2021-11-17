<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Embeddable\Address;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;

trait AddressTrait {
    #[
        ApiProperty(description: 'Adresse'),
        ORM\Embedded,
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private Address $address;

    #[Pure]
    public function __construct() {
        $this->address = new Address();
    }

    final public function getAddress(): Address {
        return $this->address;
    }

    final public function setAddress(Address $address): self {
        $this->address = $address;
        return $this;
    }
}
