<?php

namespace App\Entity\Selling\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Selling\Customer\Order\Item\CurrentPlace;
use App\Entity\Item as BaseItem;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

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
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        shortName: 'SellingOrderItem',
        denormalizationContext: [
            'groups' => ['write:item', 'write:order', 'write:current_place', 'write:notes', 'write:ref', 'write:name'],
            'openapi_definition_name' => 'SellingOrderItem-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:order', 'read:current_place', 'read:notes', 'read:ref', 'read:name'],
            'openapi_definition_name' => 'SellingOrderItem-read'
        ],
    ),
    ORM\Entity,
    ORM\DiscriminatorColumn(name: 'type', type: 'string'),
    ORM\DiscriminatorMap(Item::TYPES),
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'customer_order_item')
]
abstract class Item extends BaseItem {
    public const TYPES = ['component' => ComponentItem::class, 'product' => ProductItem::class];

    #[
        ApiProperty(description: 'Statut', required: true, example: CurrentPlace::WF_PLACE_SAVED),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:current_place', 'write:current_place'])
    ]
    protected CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Commande', required: false, example: '/api/selling-orders/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Order::class),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    protected $order;

    #[
        ApiProperty(description: 'Accusé de réception envoyé ?', required: true, example: false),
        ORM\Column(options: ['default' => false], type: 'boolean'),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private bool $arSent = false;

    public function __construct() {
        $this->currentPlace = new CurrentPlace();
    }

    public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    public function isArSent(): bool {
        return $this->arSent;
    }

    public function setArSent(bool $arSent): void {
        $this->arSent = $arSent;
    }

    public function setCurrentPlace(CurrentPlace $currentPlace): void {
        $this->currentPlace = $currentPlace;
    }
}
