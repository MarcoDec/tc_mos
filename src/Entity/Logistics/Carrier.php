<?php

namespace App\Entity\Logistics;

use App\ApiPlatform\Core\Annotation\ApiProperty;
use App\ApiPlatform\Core\Annotation\ApiSerializerGroups;
use App\Entity\Embeddable\Address;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Transporteur.
 */
#[
    ApiSerializerGroups(inheritedRead: ['Carrier-read' => ['Carrier-write', 'Entity']], write: ['Carrier-write']),
    ORM\Entity
]
class Carrier extends Entity {
    #[
        ApiProperty(readRef: 'Address-read', writeRef: 'Address-write'),
        ORM\Embedded,
        Serializer\Groups(groups: ['Carrier-read', 'Carrier-write'])
    ]
    private Address $address;

    /**
     * @var null|string Nom
     */
    #[
        ApiProperty(example: 'DHL', format: 'name'),
        Assert\Length(min: 3, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50),
        Serializer\Groups(groups: ['Carrier-read', 'Carrier-write'])
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
