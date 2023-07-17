<?php

namespace App\Entity\Selling\Order;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Project\Product\Product;
use App\Filter\RelationFilter;
use App\Repository\Selling\Order\ProductItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Filter\RelationFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\Selling\Order\ProductItemRepository;

/**
 * @template-extends Item<Product>
 */
#[   
    ApiFilter(filterClass: RelationFilter::class, properties: ['item',  'order.customer']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['oder.customer' => 'partial', 'ref' => 'partial', 'requestedQuantity.value' => 'partial', 'requestedQuanity.code' => 'partial', 'confirmedQuantity.code' => 'partial', 'confirmedQuantity.value' => 'partial', 'confirmedDate' => 'partial', 'requestedDate' => 'partial']),

    ApiResource(
        description: 'Ligne de commande',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une ligne',
                    'summary' => 'Créer une ligne',
                    'tags' => ['SellingOrderItem']
                ]
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les lignes',
                    'summary' => 'Récupère les lignes',
                ],
                'path' => '/selling-order-products',
            ]
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        shortName: 'SellingOrderItemProduct',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:item', 'write:measure'],
            'openapi_definition_name' => 'SellingOrderItemProduct-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:measure', 'read:state'],
            'openapi_definition_name' => 'SellingOrderItemProduct-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity(repositoryClass: ProductItemRepository::class)
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
