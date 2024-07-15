<?php

namespace App\Entity\Purchase\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\ItemType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Project\Product\Product;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template-extends Item<Product>
 */
#[
    ApiResource(
        description: 'Ligne de commande',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une ligne',
                    'summary' => 'Créer une ligne',
                    'tags' => ['PurchaseOrderItem']
                ]
            ]
        ],
        itemOperations: ['get', 'patch', 'delete'],
        shortName: 'PurchaseOrderItemProduct',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:item', 'write:measure'],
            'openapi_definition_name' => 'PurchaseOrderItemProduct-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:measure'],
            'openapi_definition_name' => 'PurchaseOrderItemProduct-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity()
] /*repositoryClass: ProductItemRepository::class*/
class ProductItem extends Item {
    #[
        ApiProperty(description: 'Produit', example: '/api/products/1'),
        ORM\JoinColumn(name: 'product_id'),
        ORM\ManyToOne(targetEntity: Product::class, fetch: "EAGER"),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected $item;
    final protected function getType(): string {
        return ItemType::TYPE_PRODUCT;
    }
}
