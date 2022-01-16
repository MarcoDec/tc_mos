<?php

namespace App\Entity\Purchase\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Purchase\Order\Item\CurrentPlace;
use App\Entity\Item as BaseItem;
use App\Entity\Logistics\Order\Receipt;
use App\Entity\Management\Society\Company;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

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
        shortName: 'PurchaseOrderItem',
        denormalizationContext: [
            'groups' => ['write:item', 'write:order', 'write:current_place', 'write:notes', 'write:ref', 'write:name', 'write:receipt'],
            'openapi_definition_name' => 'PurchaseOrderItem-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:order', 'read:current_place', 'read:notes', 'read:ref', 'read:name', 'read:receipt'],
            'openapi_definition_name' => 'PurchaseOrderItem-read'
        ],
    ),
    ORM\Entity,
    ORM\DiscriminatorColumn(name: 'type', type: 'string'),
    ORM\DiscriminatorMap(Item::TYPES),
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'supplier_item')
]
abstract class Item extends BaseItem {
    public const TYPES = ['component' => ComponentItem::class, 'product' => ProductItem::class];

    #[
        ApiProperty(description: 'Statut', required: true, example: CurrentPlace::WF_PLACE_CANCELLED),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:current_place', 'write:current_place'])
    ]
    protected CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Commande', required: false, example: '/api/purchase-orders/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Order::class),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    protected $order;

    /**
     * @var Collection<int, Receipt>
     */
    #[
        ApiProperty(description: 'Reçus', required: false, example: ['/api/receipts/1', '/api/receipts/6']),
        ORM\OneToMany(fetch: 'EAGER', targetEntity: Receipt::class, mappedBy: 'item'),
        Serializer\Groups(['read:receipt', 'write:receipt'])
    ]
    protected Collection $receipts;

    #[
        ApiProperty(description: 'Prix du cuivre', required: true, example: 0),
        ORM\Column(type: 'float', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private float $copperPrice = 0;

    #[
        ApiProperty(description: 'Employé', required: false, readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Company::class),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private ?Company $targetCompany = null;

    public function __construct() {
        $this->currentPlace = new CurrentPlace();
        $this->receipts = new ArrayCollection();
    }

    abstract public function getStockType(): string;

    final public function addReceipt(Receipt $receipt): self {
        if (!$this->receipts->contains($receipt)) {
            $this->receipts->add($receipt);
            $receipt->setItem($this);
        }

        return $this;
    }

    final public function getCopperPrice(): float {
        return $this->copperPrice;
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    public function getReceiptQuantity(): float {
        return collect($this->receipts->toArray())->sum->getQuantity();
    }

    /**
     * @return Collection<int, Receipt>
     */
    final public function getReceipts(): Collection {
        return $this->receipts;
    }

    final public function getTargetCompany(): ?Company {
        return $this->targetCompany;
    }

    final public function getTrafficLight(): int {
        return $this->currentPlace->getTrafficLight();
    }

    final public function removeReceipt(Receipt $receipt): self {
        if ($this->receipts->contains($receipt)) {
            $this->receipts->removeElement($receipt);
            if ($receipt->getItem() === $this) {
                $receipt->setItem(null);
            }
        }

        return $this;
    }

    final public function setCopperPrice(float $copperPrice): self {
        $this->copperPrice = $copperPrice;
        return $this;
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
        return $this;
    }

    final public function setTargetCompany(?Company $targetCompany): self {
        $this->targetCompany = $targetCompany;
        return $this;
    }
}
