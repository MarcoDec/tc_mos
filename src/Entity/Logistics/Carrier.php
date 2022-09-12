<?php

namespace App\Entity\Logistics;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\EntityId;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['name']),
    ApiFilter(filterClass: OrderFilter::class, id: 'address-sorter', properties: Address::sorter),
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
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un transporteur',
                    'summary' => 'Supprime un transporteur',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un transporteur',
                    'summary' => 'Modifie un transporteur',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:address', 'write:carrier'],
            'openapi_definition_name' => 'Carrier-write'
        ],
        normalizationContext: [
            'groups' => ['read:address', 'read:carrier', 'read:id'],
            'openapi_definition_name' => 'Carrier-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class Carrier extends EntityId {
    #[
        ApiProperty(description: 'Adresse'),
        Assert\Valid,
        ORM\Embedded,
        Serializer\Groups(['read:carrier', 'write:carrier'])
    ]
    private Address $address;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'DHL'),
        Assert\Length(min: 3, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50),
        Serializer\Groups(['read:carrier', 'write:carrier'])
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
