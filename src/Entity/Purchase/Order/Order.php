<?php

namespace App\Entity\Purchase\Order;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Project\Product\KindType;
use App\Entity\Embeddable\Closer;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Purchase\Order\Order\State;
use App\Entity\Entity;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Order\Attachment\PurchaseOrderAttachment;
use App\Entity\Purchase\Supplier\Contact;
use App\Entity\Purchase\Supplier\Supplier;
use App\Entity\Selling\Order\Order as SellingOrder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Filter\RelationFilter;
use App\Filter\SetFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[
    ApiFilter(SetFilter::class, properties: ['embState.state', 'embBlocker.state']),
    ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'ref' => 'partial', 'orderFamily' => 'partial', 'kind' => 'partial']),
    ApiFilter(RelationFilter::class, properties: ['company', 'contact', 'purchaseCompany', 'deliveryCompany', 'items', 'sellingOrder', 'supplier']),
    ApiResource(
        description: 'Commande',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les commandes',
                    'summary' => 'Récupère les commandes',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une commande',
                    'summary' => 'Créer une commande',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une commande',
                    'summary' => 'Supprime une commande',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une commande',
                    'summary' => 'Récupère une commande',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une commande',
                    'summary' => 'Modifie une commande',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite la commande à son prochain statut de workflow',
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
                            'schema' => ['enum' => ['purchase_order', 'closer'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite la commande à son prochain statut de workflow'
                ],
                'path' => '/purchase-orders/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')',
                'validate' => false
            ]
        ],
        shortName: 'PurchaseOrder',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:order'],
            'openapi_definition_name' => 'PurchaseOrder-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:order', 'read:state'],
            'openapi_definition_name' => 'PurchaseOrder-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity,
    ORM\Table(name: 'purchase_order')
]
class Order extends Entity {
    #[
        ApiProperty(description: 'Compagnie', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?Company $company = null;

    #[
        ApiProperty(description: 'Contact fournisseur', readableLink: false, example: '/api/supplier-contacts/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?Contact $contact = null;

    #[
        ApiProperty(description: 'Compagnie destinataire des expéditions fournisseur', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?Company $deliveryCompany = null;

    #[
       ORM\OneToMany(mappedBy: 'order',targetEntity: PurchaseOrderAttachment::class)
       ]
    private Collection $deliveryForms;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:order', 'read:supplier:receipt'])
    ]
    private Closer $embBlocker;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:order'])
    ]
    private State $embState;
    #[
        ApiProperty(description: 'Ouverte', example: true),
        ORM\Column(type: 'boolean', options: ['default' => true]),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private bool $isOpenOrder = true;
    #[
        ApiProperty(description: 'Type', example: KindType::TYPE_PROTOTYPE, openapiContext: ['enum' => KindType::TYPES]),
        Assert\Choice(choices: KindType::TYPES),
        ORM\Column(type: 'product_kind', options: ['default' => KindType::TYPE_SERIES]),
        Serializer\Groups(['read:order', 'write:order', 'read:item'])
    ]
    private ?string $kind = KindType::TYPE_SERIES;
    const FAMILY_FREE = 'free'; // Commande libre (sans lien avec un produit)
    const FAMILY_FIXED = 'fixed'; // Commande ferme (avec lien avec un produit) mais hors EDI
    const FAMILY_FORECAST = 'forecast'; // Commande de prévisionnelle (avec lien avec un produit) mais hors EDI
    const FAMILY_EDI_ORDERS = 'edi_orders'; // Commande ferme EDI (avec lien avec un produit)
    const FAMILY_EDI_DELFOR = 'edi_delfor'; // Commande de prévisionnelle EDI (avec lien avec un produit)
    const FAMILY_EDI_ORDCHG = 'edi_ordchg'; // Commande de changement de commande EDI (avec lien avec un produit)
    #[
        ApiProperty(description: 'Famille', example: 'fixed', openapiContext: ['enum' => [self::FAMILY_FREE, self::FAMILY_FIXED, self::FAMILY_FORECAST, self::FAMILY_EDI_ORDERS, self::FAMILY_EDI_DELFOR, self::FAMILY_EDI_ORDCHG]]),
        ORM\Column(type: 'string', options: ['default' => self::FAMILY_FIXED]),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private string $orderFamily = '';

    /** @var Collection<int, Item<Component|Product>> */
    #[ORM\OneToMany(mappedBy: 'parentOrder', targetEntity: Item::class, fetch: 'EAGER')]
    
    private Collection $items;

    #[
        ApiProperty(description: 'Notes', example: 'Lorem ipsum'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Commande du client', readableLink: false, example: '/api/selling-orders/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?SellingOrder $sellingOrder = null;

    #[
        ApiProperty(description: 'Référence', example: 'EJZ65'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:order', 'read:supplier:receipt', 'write:order'])
    ]
    private ?string $ref = null;

    #[
        ApiProperty(description: 'Supplément pour le fret', example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private bool $supplementFret = false;
    #[
        ApiProperty(description: 'Total prix fixe', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private Measure $totalFixedPrice;

    #[
        ApiProperty(description: 'Total prix prévisionnel', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private Measure $totalForecastPrice;
    #[
        ApiProperty(description: 'Fournisseur', readableLink: false, example: '/api/suppliers/1'),
        ORM\ManyToOne(inversedBy: 'orders'),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?Supplier $supplier = null;

    public function __construct() {
        $this->embBlocker = new Closer();
        $this->embState = new State();
        $this->items = new ArrayCollection();
        $this->totalFixedPrice = new Measure();
        $this->totalForecastPrice = new Measure();
        $this->deliveryForms = new ArrayCollection();
    }

    /**
     * @param Item<Component|Product> $item
     */
    final public function addItem(Item $item): self {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setParentOrder($this);
        }
        return $this;
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
    }

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getContact(): ?Contact {
        return $this->contact;
    }

    final public function getDeliveryCompany(): ?Company {
        return $this->deliveryCompany;
    }

    final public function getEmbBlocker(): Closer {
        return $this->embBlocker;
    }

    final public function getEmbState(): State {
        return $this->embState;
    }

    /**
     * @return Collection<int, Item<Component|Product>>
     */
    public function getItems(): Collection {
        return $this->items;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getSellingOrder(): ?SellingOrder {
        return $this->sellingOrder;
    }

    final public function getRef(): ?string {
        return $this->ref;
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    final public function getSupplier(): ?Supplier {
        return $this->supplier;
    }

    final public function hasNoReceipt(): bool {
        foreach ($this->items as $item) {
            if ($item->hasReceipt()) {
                return false;
            }
        }
        return true;
    }

    final public function isNotReceipt(): bool {
        return !$this->isReceipt();
    }

    final public function isReceipt(): bool {
       /** @var Item<Component|Product> $item */
       foreach ($this->items as $item) {
            if ($item->isNotReceipt()) {
                return false;
            }
        }
        return true;
    }

    final public function isSupplementFret(): bool {
        return $this->supplementFret;
    }

   /**
    * @param Item<Component|Product> $item
    * @return Order
    */
    final public function removeItem(Item $item): self {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            if ($item->getParentOrder() === $this) {
                $item->setParentOrder(null);
            }
        }
        return $this;
    }

    final public function setBlocker(string $state): self {
        $this->embBlocker->setState($state);
        return $this;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setContact(?Contact $contact): self {
        $this->contact = $contact;
        return $this;
    }

    final public function setDeliveryCompany(?Company $deliveryCompany): self {
        $this->deliveryCompany = $deliveryCompany;
        return $this;
    }

    final public function setEmbBlocker(Closer $embBlocker): self {
        $this->embBlocker = $embBlocker;
        return $this;
    }

    final public function setEmbState(State $embState): self {
        $this->embState = $embState;
        return $this;
    }

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }

    final public function setSellingOrder(?SellingOrder $sellingOrder): self {
        $this->sellingOrder = $sellingOrder;
        return $this;
    }

    final public function setRef(?string $ref): self {
        $this->ref = $ref;
        return $this;
    }

    final public function setState(string $state): self {
        $this->embState->setState($state);
        return $this;
    }

    final public function setSupplementFret(bool $supplementFret): self {
        $this->supplementFret = $supplementFret;
        return $this;
    }

    final public function setSupplier(?Supplier $supplier): self {
        $this->supplier = $supplier;
        return $this;
    }

    public function getDeliveryForms(): Collection
    {
        return $this->deliveryForms;
    }

    public function setDeliveryForms(Collection $deliveryForms): void
    {
        $this->deliveryForms = $deliveryForms;
    }

    public function getIsOpenOrder(): bool
    {
        return $this->isOpenOrder;
    }

    public function setIsOpenOrder(bool $isOpenOrder): void
    {
        $this->isOpenOrder = $isOpenOrder;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(?string $kind): void
    {
        $this->kind = $kind;
    }

    public function getOrderFamily(): string
    {
        return $this->orderFamily;
    }

    public function setOrderFamily(string $orderFamily): void
    {
        $this->orderFamily = $orderFamily;
    }

    public function getTotalFixedPrice(): Measure
    {
        return $this->totalFixedPrice;
    }

    public function setTotalFixedPrice(Measure $totalFixedPrice): void
    {
        $this->totalFixedPrice = $totalFixedPrice;
    }

    public function getTotalForecastPrice(): Measure
    {
        return $this->totalForecastPrice;
    }

    public function setTotalForecastPrice(Measure $totalForecastPrice): void
    {
        $this->totalForecastPrice = $totalForecastPrice;
    }
    public function updateTotalPurchasePrice(): void
    {
        $totalFixedPrice = new Measure();
        $totalFixedPrice->setCode($this->supplier->getCurrency()->getCode());
        $totalFixedPrice->setValue(0);
        $totalForecastPrice = new Measure();
        $totalForecastPrice->setCode($this->supplier->getCurrency()->getCode());
        $totalForecastPrice->setValue(0);
        /** @var Item $item */
        foreach ($this->getItems() as $item) {
            if ($item->getIsForecast()) {
                $totalForecastPrice->add($item->getTotalItemPrice());
            } else {
                $totalFixedPrice->add($item->getTotalItemPrice());
            }
        }
        $this->setTotalFixedPrice($totalFixedPrice);
        $this->setTotalForecastPrice($totalForecastPrice);
    }
}
