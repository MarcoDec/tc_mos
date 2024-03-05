<?php

namespace App\Entity\Purchase\Order;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Collection;
use App\Controller\Purchase\Order\ItemSupplierControler;
use App\Doctrine\DBAL\Types\ItemType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Purchase\Order\Item\Closer;
use App\Entity\Embeddable\Purchase\Order\Item\State;
use App\Entity\Item as BaseItem;
use App\Entity\Logistics\Order\Receipt;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Project\Product\Family as ProductFamily;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Component\Family as ComponentFamily;
use App\Entity\Purchase\Supplier\Supplier;
use App\Entity\Quality\Reception\Check;
use App\Filter\RelationFilter;
use App\Filter\SetFilter;
use App\Repository\Purchase\Order\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template I of \App\Entity\Purchase\Component\Component|\App\Entity\Project\Product\Product
 *    ApiFilter(filterClass: SearchFilter::class, properties: ['confirmedDate' => 'partial', 'requestedDate' => 'partial', 'confirmedQuantity' => 'partial', 'requestedQuantity' => 'partial']),

 * @template-extends BaseItem<I, Order>
 *
 */
#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['order']),
    ApiFilter(filterClass: SetFilter::class, properties: ['embState.state']),
    ApiResource(
        description: 'Ligne de commande',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les lignes',
                    'summary' => 'Récupère les lignes',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\') or is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
            ],
            'filtreSupplier' => [
                'controller' => ItemSupplierControler::class,
                'method' => 'GET',
                'openapi_context' => [
                    'description' => 'Filtrer par fournisseur',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'api',
                        'schema' => [
                            'type' => 'integer',
                        ]
                    ]],
                    'summary' => 'Filtrer par fournisseur'
                ],
                'path' => '/purchase-order-items/supplierFilter/{api}',
                'read' => false,
                'write' => false
            ],
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
        paginationClientEnabled: true
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'item'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity(repositoryClass: ItemRepository::class),
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
        ApiProperty(description: 'Prix du', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected Measure $price;


    #[
        ORM\Embedded,
        Serializer\Groups(['read:item']),
    ]
    protected Closer $embBlocker;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:item'])
    ]
    protected State $embState;

    #[
        ApiProperty(description: 'Commande', readableLink: false, example: '/api/purchase-orders/1'),
        ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items'),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected $order;

    #[
        ApiProperty(description: 'Employé', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected ?Company $targetCompany = null;

    /** @var DoctrineCollection<int, Receipt<I>> */
    #[ORM\OneToMany(mappedBy: 'item', targetEntity: Receipt::class)]
    
    private DoctrineCollection $receipts;

    public function __construct() {
        parent::__construct();
        $this->embBlocker = new Closer();
        $this->embState = new State();
        $this->receipts = new ArrayCollection();
    }

    /**
     * @param Receipt<I> $receipt
     *
     * @return $this
     */
    final public function addReceipt(Receipt $receipt): self {
        if (!$this->receipts->contains($receipt)) {
            $this->receipts->add($receipt);
            $receipt->setItem($this);
        }
        return $this;
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
    }
    /**
     * @return Collection<int, Check<I, Company|Component|ComponentFamily|Product|ProductFamily|Supplier>>
     */
    final public function getChecks(): Collection {
        $checks = $this->getReceiptChecks();
        $createdChecks = $this->getItemChecks()->merge($this->getCompanyChecks())->merge($this->getSupplierChecks());
        /** @var Receipt<I> $receipt */
        $receipt = (new Receipt())->setItem($this);
        foreach ($createdChecks as $check) {
            if (!empty($id = $check->getReference()?->getId()) && $id > 0 && !$checks->has($id)) {
                /** @phpstan-ignore-next-line */
                $checks->put($id, $check->setReceipt($receipt));
            }
        }
        return $checks;
    }

    /**
     * @return Collection<int, Check<Component|Product, Company>>
     */
    final public function getCompanyChecks(): Collection {
        return $this->getCompany()?->getChecks() ?? new Collection();
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

    /**
     * @return Collection<int, Check<Component, Component|ComponentFamily>>|Collection<int, Check<Product, Product|ProductFamily>>
     */
    final public function getItemChecks(): Collection {
        return $this->item?->getChecks() ?? new Collection();
    }
   public function getMeasures(): array {
        return array_merge(parent::getMeasures(),[$this->copperPrice/* , $this->getReceiptQuantity() */]);
   }
    /**
     * @return Collection<int, Check<I, Company|Component|ComponentFamily|Product|ProductFamily|Supplier>>
     */
    final public function getReceiptChecks(): Collection {
        /** @var Collection<int, Check<I, Company|Component|ComponentFamily|Product|ProductFamily|Supplier>> $checks */
        $checks = Collection::collect($this->receipts->getValues())
            ->map(static fn (Receipt $receipt): array => $receipt->getChecks()->getValues())
            ->flatten();
        /**
         * @param Check<I, Company|Component|ComponentFamily|Product|ProductFamily|Supplier> $check
         *
         * @return array<int, Check<I, Company|Component|ComponentFamily|Product|ProductFamily|Supplier>>
         */
        $mapper = static fn (Check $check): array => empty($id = $check->getReference()?->getId()) || $id <= 0 ? [] : [$id => $check];
        /** @phpstan-ignore-next-line */
        return $checks->mapWithKeys($mapper);
    }

    #[
        ApiProperty(description: 'Quantité réceptionnée', openapiContext: ['$ref' => '#/components/schemas/Measure-generic']),
        Serializer\Groups(['read:item']
    )]
    final public function getReceiptQuantity(): Measure {
        $quantity = (new Measure())
            ->setCode($this->getUnit()?->getCode())
            ->setUnit($this->getUnit());
        foreach ($this->getReceipts() as $receipt) {
            $quantity = $quantity->add($receipt->getQuantity());
        }
        return $quantity;
    }
    
    /***
     * ref
    */
    // #[
    //     ApiProperty(description: 'Réference', readableLink: false, example: 'azerty'),
    //     ORM\ManyToOne,
    //     Serializer\Groups(['read:item', 'write:item'])
    // ]
    // protected string $ref; 
    // final public function getRef(): string {
    //     return $this->ref;
    // }

    // final public function setRef(string $reference): self {
    //     $this->ref = $reference;
    //     return $this;
    // }
    /**
     * @return DoctrineCollection<int, Receipt<I>>
     */
    final public function getReceipts(): DoctrineCollection {
        return $this->receipts;
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    #[
        ApiProperty(description: 'Fournisseur', readableLink: false, example: '/api/suppliers/1'),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    final public function getSupplier(): ?Supplier {
        return $this->order?->getSupplier();
    }

    /**
     * @return Collection<int, Check<Component|Product, Supplier>>
     */
    final public function getSupplierChecks(): Collection {
        return $this->getSupplier()?->getChecks() ?? new Collection();
    }

    final public function getTargetCompany(): ?Company {
        return $this->targetCompany;
    }

    final public function hasNoReceipt(): bool {
        return $this->receipts->isEmpty();
    }

    final public function hasReceipt(): bool {
        return !$this->hasNoReceipt();
    }

    final public function isNotReceipt(): bool {
        return !$this->isReceipt();
    }

    final public function isReceipt(): bool {
        return $this->getReceiptQuantity()->isGreaterThanOrEqual($this->getConfirmedQuantity());
    }

    /**
     * @param Receipt<I> $receipt
     *
     * @return $this
     */
    final public function removeReceipt(Receipt $receipt): self {
        if ($this->receipts->contains($receipt)) {
            $this->receipts->removeElement($receipt);
            if ($receipt->getItem() === $this) {
                $receipt->setItem(null);
            }
        }
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
