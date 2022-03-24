<?php

namespace App\Entity\Logistics;

use ApiPlatform\Core\Annotation\ApiResource;
use App\ApiPlatform\Core\Annotation\ApiProperty;
use App\ApiPlatform\Core\Annotation\ApiSerializerGroups;
use App\Entity\Embeddable\Address;
use App\Entity\Entity;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Transporteur.
 */
#[
    ApiResource(
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les transporteurs',
                    'summary' => 'Récupère les transporteurs',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un transporteur',
                    'summary' => 'Créer un transporteur',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un transporteur',
                    'summary' => 'Supprime un transporteur',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un transporteur',
                    'summary' => 'Modifie un transporteur',
                ]
            ]
        ],
        denormalizationContext: [
            'groups' => ['Address-write', 'Carrier-write'],
            'openapi_definition_name' => 'Carrier-write'
        ],
        normalizationContext: [
            'groups' => ['Address-read', 'Carrier-read'],
            'openapi_definition_name' => 'Carrier-read'
        ]
    ),
    ApiSerializerGroups(inheritedRead: ['Carrier-read' => ['Carrier-write', 'Entity']], write: ['Carrier-write']),
    ORM\Entity,
    UniqueEntity('name')
]
class Carrier extends Entity {
    #[
        Assert\Valid,
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
