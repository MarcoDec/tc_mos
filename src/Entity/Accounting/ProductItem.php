<?php

namespace App\Entity\Accounting;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Project\Product\Product;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Item du produit',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un item de produit',
                    'summary' => 'Créer un item de produit',
                ]
            ],
        ],
        itemOperations: [
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_WRITER.'\')'
        ],
        shortName: 'AccountingProduct',
        denormalizationContext: [
            'groups' => ['write:item', 'write:order', 'write:current_place', 'write:notes', 'write:ref', 'write:name', 'write:product'],
            'openapi_definition_name' => 'AccountingProduct-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:order', 'read:current_place', 'read:notes', 'read:ref', 'read:name', 'read:product'],
            'openapi_definition_name' => 'AccountingProduct-read'
        ],
    ),
    ORM\Entity
]
class ProductItem extends Item {
    #[
        ApiProperty(description: 'Produit', required: false, example: '/api/products/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Product::class),
        Serializer\Groups(['read:product', 'write:product'])
    ]
    protected $item;
}
