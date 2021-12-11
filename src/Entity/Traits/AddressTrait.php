<?php

namespace App\Entity\Traits;

use App\Entity\Embeddable\Address;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait AddressTrait {
    #[
        Assert\Valid,
        ORM\Embedded(Address::class),
    ]
    protected $address;

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
