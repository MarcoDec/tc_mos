<?php

namespace App\Entity\Selling\Order;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Doctrine\DBAL\Types\ItemType;
use App\Entity\Embeddable\Closer;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Selling\Order\Item\State;
use App\Entity\Management\Currency;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Item as BaseItem;
use App\Entity\Production\Manufacturing\Expedition;
use App\Entity\Selling\Customer\Price;
use App\Filter\RelationFilter;
use App\Repository\Selling\Order\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


/**
 * @template I of Component|Product
 *
 * @template-extends BaseItem<I, Order>
 */
#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['id' => 'exact', 'item.id' => 'exact', 'parentOrder.id' => 'exact', 'ref' => 'partial', 'embState.state' => 'exact', 'confirmedDate' => 'exact', 'confirmedQuantity.value' => 'exact', 'confirmedQuantity.code' => 'exact', 'requestedDate' => 'exact', 'requestedQuantity.value' => 'exact', 'requestedQuantity.code' => 'exact', 'notes' => 'partial']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['item', 'parentOrder', 'ref', 'embState.state', 'confirmedDate', 'confirmedQuantity.value', 'confirmedQuantity.code', 'requestedDate', 'requestedQuantity.value', 'requestedQuantity.code', 'notes']),
    ApiFilter(filterClass: OrderFilter::class, properties: ['id', 'item.id', 'ref', 'embState.state', 'confirmedDate', 'confirmedQuantity.value', 'requestedDate', 'requestedQuantity.value', 'notes']),

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
        ApiProperty(description: 'Estimation ?', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected bool $isForecast = false;

    #[
        ApiProperty(description: 'Commande Client', readableLink: false, example: '/api/selling-orders/1', fetchEager: false),
        ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'sellingOrderItems'),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected $parentOrder;

    #[
        ApiProperty(description: 'Expéditions associées', example: '/api/expeditions/1', fetchEager: false),
        ORM\OneToMany(mappedBy: 'item', targetEntity: Expedition::class),
        Serializer\Groups(['read:item'])
    ]
    private Collection $expeditions;

    #[
        ApiProperty(description: 'Quantité expédiée', fetchEager: false),
        ORM\Embedded(class: Measure::class),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private Measure $sentQuantity;

    #[
        ApiProperty(description: 'Prix total de l\'item', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private Measure $totalItemPrice;

    public function __construct() {
        parent::__construct();
        $this->embBlocker = new Closer();
        $this->embState = new State();
        $this->expeditions = new ArrayCollection();
        $this->parentOrder = new Order();
    }
    /*, 'read:expedition'*/
    #[
        ApiProperty(description: 'Client'),
        Serializer\Groups(['read:item','write:item'])
    ]
    final public function getCustomer(): ?Customer {
        return $this->parentOrder->getCustomer();
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
        ApiProperty(description: 'Item commandé', readableLink: false, example: '/api/products/1'),
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

    /**
     * @return bool
     */
    public function getIsForecast(): bool
    {
        return $this->isForecast;
    }

    /**
     * @param bool $isForecast
     * @return Item
     */
    public function setIsForecast(bool $isForecast): Item
    {
        $this->isForecast = $isForecast;
        return $this;
    }

    public function getSentQuantity(): Measure
    {
        return $this->sentQuantity;
    }

    public function setSentQuantity(Measure $sentQuantity): void
    {
        $this->sentQuantity = $sentQuantity;
    }
    #[
        ApiProperty(description: 'Quantité restante à envoyer', readableLink: false),
        Serializer\Groups(['read:item'])
    ]
    public function getQuantityToBeSent(): Measure
    {
        return $this->getRequestedQuantity()->substract($this->sentQuantity);
    }

    public function getTotalItemPrice(): Measure
    {
        return $this->totalItemPrice;
    }

    public function setTotalItemPrice(Measure $totalItemPrice): void
    {
        $this->totalItemPrice = $totalItemPrice;
    }

}
