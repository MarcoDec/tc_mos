<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['customer']),
    ApiResource(
        description: 'Adresse de facturation',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les adresses de facturation',
                    'summary' => 'Récupère les adresses de facturation',
                    'tags' => ['CustomerAddress']
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une adresse de facturation',
                    'summary' => 'Créer une adresse de facturation',
                    'tags' => ['CustomerAddress']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        itemOperations: ['get', 'patch', 'delete'],
        shortName: 'BillingAddress',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:address', 'write:customer-address'],
            'openapi_definition_name' => 'BillingAddress-write'
        ],
        normalizationContext: [
            'groups' => ['read:address', 'read:customer-address', 'read:id'],
            'openapi_definition_name' => 'BillingAddress-read',
            'skip_null_values' => false
        ],
        paginationEnabled: false
    ),
    ORM\Entity
]
class BillingAddress extends Address {
}
