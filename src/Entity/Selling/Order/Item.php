<?php

namespace App\Entity\Selling\Order;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\ItemType;
use App\Entity\Embeddable\Closer;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Selling\Order\Item\State;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Item as BaseItem;
use App\Entity\Production\Manufacturing\Expedition;
use App\Filter\RelationFilter;
use App\Repository\Selling\Order\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


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
                            'schema' => ['enum' => ['selling_order_item', 'closer'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite la ligne à son prochain statut de workflow'
                ],
                'path' => '/selling-order-items/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')',
                'validate' => false
            ]
        ],
        shortName: 'SellingOrderItem',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:item', 'write:measure'],
            'openapi_definition_name' => 'SellingOrderItem-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:measure', 'read:state', 'read:order', 'read:customer'],
            'openapi_definition_name' => 'SellingOrderItem-read',
            'skip_null_values' => false
        ]
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'item'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity(repositoryClass: ItemRepository::class),
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'selling_order_item')
]
abstract class Item extends BaseItem {
    final public const TYPES = [ItemType::TYPE_COMPONENT => ComponentItem::class, ItemType::TYPE_PRODUCT => ProductItem::class];

    #[
        ApiProperty(description: 'Accusé de réception envoyé ?', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected bool $arSent = false;

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
        ApiProperty(description: 'Commande', readableLink: false, example: '/api/selling-orders/1'),
        ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'sellingOrderItems', fetch: "EAGER"),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected $order;

    #[
        ApiProperty(description: 'Expéditions associées', example: '/api/expeditions/1'),
        ORM\OneToMany(targetEntity: Expedition::class, mappedBy: 'item'),
        Serializer\Groups(['read:item'])
    ]
    private Collection $expeditions;

    public function __construct() {
        parent::__construct();
        $this->embBlocker = new Closer();
        $this->embState = new State();
        $this->expeditions = new ArrayCollection();
    }
    /*, 'read:expedition'*/
    #[
        ApiProperty(description: 'Client'),
        Serializer\Groups(['read:item','write:item'])
    ]
    final public function getCustomer(): ?Customer {
        return $this->order->getCustomer();
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
    }

    final public function getEmbBlocker(): Closer {
        return $this->embBlocker;
    }

    final public function getEmbState(): State {
        return $this->embState;
    }

    final public function getExpeditions(): Collection {
        return $this->expeditions;
    }

    #[
        ApiProperty(description: 'Item', readableLink: false, example: '/api/selling-orders/1'),
        Serializer\Groups(['read:item'])
    ]
    final public function getItem() {
        return $this->item;
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

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
    final public function setExpeditions(Collection $expeditions): self {
        $this->expeditions = $expeditions;

        foreach ($expeditions as $expedition) {
            $expedition->setItem($this);
        }
        return $this;
    }

    /**
     * @return $this
     */
    final public function setState(string $state): self {
        $this->embState->setState($state);
        return $this;
    }
}
