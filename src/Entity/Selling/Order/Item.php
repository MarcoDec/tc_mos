<?php

namespace App\Entity\Selling\Order;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\ItemType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Selling\Order\Item\CurrentPlace;
use App\Entity\Interfaces\WorkflowInterface;
use App\Entity\Item as BaseItem;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template I of \App\Entity\Purchase\Component\Component|\App\Entity\Project\Product\Product
 *
 * @template-extends BaseItem<I, Order>
 */
#[
    ApiResource(
        description: 'Ligne de commande',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les lignes',
                    'summary' => 'Récupère les lignes',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une ligne',
                    'summary' => 'Supprime une ligne',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une ligne',
                    'summary' => 'Modifie une ligne',
                ]
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite la ligne à son prochain statut de workflow',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'transition',
                        'required' => true,
                        'schema' => [
                            'enum' => CurrentPlace::TRANSITIONS,
                            'type' => 'string'
                        ]
                    ]],
                    'requestBody' => null,
                    'summary' => 'Transite le la ligne à son prochain statut de workflow'
                ],
                'path' => '/customer-order-items/{id}/promote/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')',
                'validate' => false
            ]
        ],
        shortName: 'CustomerOrderItem',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:item', 'write:measure'],
            'openapi_definition_name' => 'CustomerOrderItem-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:measure'],
            'openapi_definition_name' => 'CustomerOrderItem-read',
            'skip_null_values' => false
        ]
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'item_type'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'customer_order_item')
]
abstract class Item extends BaseItem implements WorkflowInterface {
    public const TYPES = [ItemType::TYPE_COMPONENT => ComponentItem::class, ItemType::TYPE_PRODUCT => ProductItem::class];

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

    #[
        ApiProperty(description: 'Statut'),
        ORM\Embedded,
        Serializer\Groups(['read:customer'])
    ]
    private CurrentPlace $currentPlace;

    public function __construct() {
        parent::__construct();
        $this->currentPlace = new CurrentPlace();
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    #[Pure]
    final public function getState(): ?string {
        return $this->currentPlace->getName();
    }

    final public function isArSent(): bool {
        return $this->arSent;
    }

    #[Pure]
    final public function isDeletable(): bool {
        return $this->currentPlace->isDeletable();
    }

    #[Pure]
    final public function isFrozen(): bool {
        return $this->currentPlace->isFrozen();
    }

    /**
     * @return $this
     */
    final public function setArSent(bool $arSent): self {
        $this->arSent = $arSent;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setState(?string $state): self {
        $this->currentPlace->setName($state);
        return $this;
    }
}
