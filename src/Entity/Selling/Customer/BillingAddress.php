<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['customer']),
    ApiResource(
        description: 'Adresse de facturation',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les adresses de facturation',
                    'summary' => 'Récupère les adresses de facturation',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une adresse de facturation',
                    'summary' => 'Créer une adresse de facturation',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une adresse de facturation',
                    'summary' => 'Supprime une adresse de facturation',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une adresse de facturation',
                    'summary' => 'Modifie une adresse de facturation',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        shortName: 'BillingAddress',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:address', 'write:billing-address'],
            'openapi_definition_name' => 'BillingAddress-write'
        ],
        normalizationContext: [
            'groups' => ['read:address', 'read:billing-address', 'read:id'],
            'openapi_definition_name' => 'BillingAddress-read',
            'skip_null_values' => false
        ],
        paginationEnabled: false
    ),
    ORM\Entity
]
class BillingAddress extends Entity {
    #[
        ApiProperty(description: 'Adresse'),
        ORM\Embedded,
        Serializer\Groups(['billing-address', 'read:billing-address', 'write:billing-address'])
    ]
    private Address $address;

    #[
        ApiProperty(description: 'Client', readableLink: false, example: '/api/customers/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:billing-address', 'write:billing-address'])
    ]
    private ?Customer $customer;

    #[
        ApiProperty(description: 'Nom'),
        ORM\Column,
        Serializer\Groups(['billing-address', 'read:billing-address', 'write:billing-address'])
    ]
    private ?string $name = null;

    public function __construct() {
        $this->address = new Address();
    }

    final public function getAddress(): Address {
        return $this->address;
    }

    final public function getCustomer(): ?Customer {
        return $this->customer;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setAddress(Address $address): self {
        $this->address = $address;
        return $this;
    }

    final public function setCustomer(?Customer $customer): self {
        $this->customer = $customer;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
