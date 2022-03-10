<?php

namespace App\Entity\Logistics;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiFilter(filterClass: SearchFilter::class, id: 'address', properties: Address::filter),
    ApiResource(
        description: 'Transporteur',
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
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['Address-write', 'Carrier-write'],
            'openapi_definition_name' => 'Carrier-write'
        ],
        normalizationContext: [
            'groups' => ['Address-read', 'Carrier-read', 'Entity:id'],
            'openapi_definition_name' => 'Carrier-read'
        ]
    ),
    ORM\Entity
]
class Carrier extends Entity {
    #[
        ApiProperty(description: 'Adresse'),
        ORM\Embedded,
        Serializer\Groups(['Carrier-read', 'Carrier-write'])
    ]
    private Address $address;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'DHL'),
        Assert\Length(min: 3, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50),
        Serializer\Groups(['Carrier-read', 'Carrier-write'])
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
