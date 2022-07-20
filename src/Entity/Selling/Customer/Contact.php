<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Management\Society\Contact as SocietyContact;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template-extends SocietyContact<Customer>
 */
#[
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'customer' => 'name',
    ]),
    ApiResource(
        description: 'Contact',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les contacts',
                    'summary' => 'Récupère les contacts',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un contact',
                    'summary' => 'Créer un contact',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un contact',
                    'summary' => 'Supprime un contact',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_ADMIN.'\')'
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un contact',
                    'summary' => 'Modifie un contact',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        shortName: 'CustomerContact',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:customer-contact', 'write:name', 'write:address', 'write:society-contact'],
            'openapi_definition_name' => 'CustomerContact-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:address', 'read:customer-contact', 'read:society-contact'],
            'openapi_definition_name' => 'CustomerContact-read'
        ],
    ),
    ORM\Entity
]
class Contact extends SocietyContact {
    #[
        ApiProperty(description: 'Client', readableLink: false, example: '/api/customers/7'),
        ORM\ManyToOne(targetEntity: Customer::class),
        Serializer\Groups(['read:customer-contact', 'write:customer-contact'])
    ]
    protected $society;
}
