<?php

namespace App\Entity\Accounting;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Project\Product\Product;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template-extends Item<Product>
 */
#[
    ApiResource(
        description: 'Lignes facturée',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une ligne',
                    'summary' => 'Créer une ligne',
                    'tags' => ['BillItem']
                ]
            ],
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        shortName: 'BillItemProduct',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_WRITER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:item', 'write:measure'],
            'openapi_definition_name' => 'BillItemProduct-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:measure'],
            'openapi_definition_name' => 'BillItemProduct-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity
]
class ProductItem extends Item {
    #[
        ApiProperty(description: 'Produit', readableLink: false, example: '/api/products/1'),
        ORM\JoinColumn(name: 'product_id'),
        ORM\ManyToOne(targetEntity: Product::class),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected $item;
}
