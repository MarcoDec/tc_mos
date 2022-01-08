<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'customer' => 'name',
    ]),
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
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une adresse de facturation',
                    'summary' => 'Modifie une adresse de facturation',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:address', 'write:name'],
            'openapi_definition_name' => 'BillingAddress-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:address', 'read:name'],
            'openapi_definition_name' => 'BillingAddress-read'
        ],
    ),
    ORM\Entity
]
class BillingAddress extends Address {
    #[
        ApiProperty(description: 'Client', required: false, readableLink: false, example: '/api/customers/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Customer::class),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private ?Customer $customer;

    final public function getCustomer(): ?Customer {
        return $this->customer;
    }

    final public function setCustomer(?Customer $customer): self {
        $this->customer = $customer;
        return $this;
    }
}
