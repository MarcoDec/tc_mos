<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Selling\Customer\AddressType;
use App\Entity\Embeddable\Address as EmbeddableAddress;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['customer']),
    ApiResource(
        description: 'Adresse cliente',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les adresses clientes',
                    'summary' => 'Récupère les adresses clientes',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une adresse cliente',
                    'summary' => 'Supprime une adresse cliente',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une adresse cliente',
                    'summary' => 'Modifie une adresse cliente',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        shortName: 'CustomerAddress',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:address', 'write:customer-address'],
            'openapi_definition_name' => 'CustomerAddress-write'
        ],
        normalizationContext: [
            'groups' => ['read:address', 'read:customer-address', 'read:id'],
            'openapi_definition_name' => 'CustomerAddress-read',
            'skip_null_values' => false
        ],
        paginationEnabled: false
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'customer_address_type'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'customer_address')
]
abstract class Address extends Entity {
    public const TYPES = [
        AddressType::TYPE_BILLING => BillingAddress::class,
        AddressType::TYPE_DELIVERY => DeliveryAddress::class
    ];

    #[
        ApiProperty(description: 'Adresse'),
        ORM\Embedded,
        Serializer\Groups(['read:customer-address', 'write:customer-address'])
    ]
    private EmbeddableAddress $address;

    #[
        ApiProperty(description: 'Client', readableLink: false, example: '/api/customers/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:customer-address', 'write:customer-address'])
    ]
    private ?Customer $customer;

    #[
        ApiProperty(description: 'Nom'),
        ORM\Column,
        Serializer\Groups(['read:customer-address', 'write:customer-address'])
    ]
    private ?string $name = null;

    public function __construct() {
        $this->address = new EmbeddableAddress();
    }

    final public function getAddress(): EmbeddableAddress {
        return $this->address;
    }

    final public function getCustomer(): ?Customer {
        return $this->customer;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setAddress(EmbeddableAddress $address): self {
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
