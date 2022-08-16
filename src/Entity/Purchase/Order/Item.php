<?php

namespace App\Entity\Purchase\Order;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\ItemType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Purchase\Order\Item\CurrentPlace;
use App\Entity\Interfaces\WorkflowInterface;
use App\Entity\Item as BaseItem;
use App\Entity\Management\Society\Company\Company;
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
        ],
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'item_type'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'supplier_order_item')
]
abstract class Item extends BaseItem implements WorkflowInterface {
    public const TYPES = [ItemType::TYPE_COMPONENT => ComponentItem::class, ItemType::TYPE_PRODUCT => ProductItem::class];

    #[
        ApiProperty(description: 'Commande', readableLink: false, example: '/api/supplier-orders/1'),
        ORM\ManyToOne(targetEntity: Order::class),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected $order;

    #[
        ApiProperty(description: 'Prix du cuivre', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private Measure $copperPrice;

    #[
        ApiProperty(description: 'Statut'),
        ORM\Embedded,
        Serializer\Groups(['read:customer'])
    ]
    private CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Employé', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private ?Company $targetCompany = null;

    public function __construct() {
        parent::__construct();
        $this->currentPlace = new CurrentPlace();
    }

    final public function getCopperPrice(): Measure {
        return $this->copperPrice;
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    #[Pure]
    final public function getState(): ?string {
        return $this->currentPlace->getName();
    }

    final public function getTargetCompany(): ?Company {
        return $this->targetCompany;
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
    final public function setCopperPrice(Measure $copperPrice): self {
        $this->copperPrice = $copperPrice;
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

    /**
     * @return $this
     */
    final public function setTargetCompany(?Company $targetCompany): self {
        $this->targetCompany = $targetCompany;
        return $this;
    }
}
