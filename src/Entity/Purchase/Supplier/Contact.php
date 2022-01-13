<?php

namespace App\Entity\Purchase\Supplier;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Management\Society\Contact as SocietyContact;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'supplier' => 'name',
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
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un contact',
                    'summary' => 'Supprime un contact',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un contact',
                    'summary' => 'Modifie un contact',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        shortName: 'SupplierContact',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:supplier-contact', 'write:name', 'write:address', 'write:society-contact'],
            'openapi_definition_name' => 'SupplierContact-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:address', 'read:supplier-contact', 'read:society-contact'],
            'openapi_definition_name' => 'SupplierContact-read'
        ],
    ),
    ORM\Entity,
   ORM\Table(name: 'supplier_contact')
]
class Contact extends SocietyContact {
    #[
        ApiProperty(description: 'Client', readableLink: false, example: '/api/suppliers/7'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Supplier::class),
        Serializer\Groups(['read:supplier-contact', 'write:supplier-contact'])
    ]
    protected $society;
}
