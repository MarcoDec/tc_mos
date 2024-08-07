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

/**
 * @template-extends SocietyContact<Supplier>
 */
#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['society', 'name' => 'partial', 'fullName' => 'partial']),
    ApiResource(
        description: 'Contact fournisseur',
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
            'get',
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
            'groups' => ['write:address', 'write:contact'],
            'openapi_definition_name' => 'SupplierContact-write'
        ],
        normalizationContext: [
            'groups' => ['read:address', 'read:contact', 'read:id'],
            'openapi_definition_name' => 'SupplierContact-read',
            'skip_null_values' => false
        ],
        paginationEnabled: false
    ),
    ORM\Entity,
    ORM\Table(name: 'supplier_contact')
]
class Contact extends SocietyContact {
    #[
        ApiProperty(description: 'Client', readableLink: false, example: '/api/suppliers/1'),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne(targetEntity: Supplier::class),
        Serializer\Groups(['read:contact', 'write:contact'])
    ]
    protected $society;
}
