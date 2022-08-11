<?php

namespace App\Entity\Purchase\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Item as BaseItem;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template I of \App\Entity\Purchase\Component\Component|\App\Entity\Project\Product\Product
 *
 * @template-extends BaseItem<I, Order>
 */
#[
    ApiResource(
        description: 'Item',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les items',
                    'summary' => 'Récupère les items',
                ]
            ]
        ],
        itemOperations: [
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un item',
                    'summary' => 'Modifie un item',
                ]
            ],
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un item',
                    'summary' => 'Supprime un item',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ]
        ],
        shortName: 'SupplierOrderItem',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:item', 'write:measure'],
            'openapi_definition_name' => 'SupplierOrderItem-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:measure'],
            'openapi_definition_name' => 'SupplierOrderItem-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\DiscriminatorColumn(name: 'type', type: 'string'),
    ORM\DiscriminatorMap(Item::TYPES),
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'supplier_order_item')
]
abstract class Item extends BaseItem {
    public const TYPES = ['component' => ComponentItem::class, 'product' => ProductItem::class];

    #[
        ApiProperty(description: 'Commande', example: '/api/selling-orders/1'),
        ORM\ManyToOne(targetEntity: Order::class),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected $order;

    #[
        ApiProperty(description: 'Accusé de réception envoyé ?', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private bool $arSent = false;

    final public function isArSent(): bool {
        return $this->arSent;
    }

    /**
     * @return $this
     */
    final public function setArSent(bool $arSent): self {
        $this->arSent = $arSent;
        return $this;
    }
}
