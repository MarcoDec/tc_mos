<?php

namespace App\Entity\Purchase\Order;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\ItemType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Purchase\Order\Item\Closer;
use App\Entity\Embeddable\Purchase\Order\Item\State;
use App\Entity\Item as BaseItem;
use App\Entity\Management\Society\Company\Company;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template I of \App\Entity\Purchase\Component\Component|\App\Entity\Project\Product\Product
 *
 * @template-extends BaseItem<I, Order>
 */
#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['order']),
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
                    'parameters' => [
                        [
                            'in' => 'path',
                            'name' => 'transition',
                            'required' => true,
                            'schema' => ['enum' => [...State::TRANSITIONS, ...Closer::TRANSITIONS], 'type' => 'string']
                        ],
                        [
                            'in' => 'path',
                            'name' => 'workflow',
                            'required' => true,
                            'schema' => ['enum' => ['purchase_order_item', 'purchase_order_item_closer'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite la ligne à son prochain statut de workflow'
                ],
                'path' => '/purchase-order-items/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')',
                'validate' => false
            ]
        ],
        shortName: 'PurchaseOrderItem',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:item', 'write:measure'],
            'openapi_definition_name' => 'PurchaseOrderItem-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:measure', 'read:state'],
            'openapi_definition_name' => 'PurchaseOrderItem-read',
            'skip_null_values' => false
        ],
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'item'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'purchase_order_item')
]
abstract class Item extends BaseItem {
    final public const TYPES = [ItemType::TYPE_COMPONENT => ComponentItem::class, ItemType::TYPE_PRODUCT => ProductItem::class];

    #[
        ApiProperty(description: 'Prix du cuivre', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected Measure $copperPrice;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:item'])
    ]
    protected Closer $embBlocker;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:item'])
    ]
    protected State $embState;

    #[
        ApiProperty(description: 'Commande', readableLink: false, example: '/api/purchase-orders/1'),
        ORM\ManyToOne(targetEntity: Order::class),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected $order;

    #[
        ApiProperty(description: 'Employé', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected ?Company $targetCompany = null;

    public function __construct() {
        parent::__construct();
        $this->embBlocker = new Closer();
        $this->embState = new State();
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
    }

    final public function getCopperPrice(): Measure {
        return $this->copperPrice;
    }

    final public function getEmbBlocker(): Closer {
        return $this->embBlocker;
    }

    final public function getEmbState(): State {
        return $this->embState;
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    final public function getTargetCompany(): ?Company {
        return $this->targetCompany;
    }

    /**
     * @return $this
     */
    final public function setBlocker(string $state): self {
        $this->embBlocker->setState($state);
        return $this;
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
    final public function setEmbBlocker(Closer $embBlocker): self {
        $this->embBlocker = $embBlocker;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setEmbState(State $embState): self {
        $this->embState = $embState;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setState(string $state): self {
        $this->embState->setState($state);
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
