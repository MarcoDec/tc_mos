<?php

namespace App\Doctrine\Entity\Logistics;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Doctrine\Entity\Embeddable\Address;
use App\Doctrine\Entity\Embeddable\Hr\Employee\Roles;
use App\Doctrine\Entity\Entity;
use App\Doctrine\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use const NO_ITEM_GET_OPERATION;
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
            'groups' => ['write:address', 'write:carrier', 'write:name'],
            'openapi_definition_name' => 'Carrier-write'
        ],
        normalizationContext: [
            'groups' => ['read:address', 'read:carrier', 'read:id', 'read:name'],
            'openapi_definition_name' => 'Carrier-read'
        ]
    ),
    ORM\Entity,
    ORM\Table
]
class Carrier extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'DHL'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Adresse'),
        ORM\Embedded,
        Serializer\Groups(['read:carrier', 'write:carrier'])
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
