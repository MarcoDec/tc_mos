<?php

namespace App\Entity\Logistics\Order;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Logistics\Order\State;
use App\Entity\Embeddable\Measure;
use App\Entity\EntityId;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Management\Unit;
use App\Entity\Project\Product\Family as ProductFamily;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Component\Family as ComponentFamily;
use App\Entity\Purchase\Order\Item;
use App\Entity\Purchase\Supplier\Supplier;
use App\Entity\Quality\Reception\Check;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template I of \App\Entity\Purchase\Component\Component|\App\Entity\Project\Product\Product
 */
#[
    ApiResource(
        description: 'Réception',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les réceptions',
                    'summary' => 'Récupère les réceptions',
                ]
            ]
        ],
        itemOperations: [
            'get' => NO_ITEM_GET_OPERATION,
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite la réception à son prochain statut de workflow',
                    'parameters' => [
                        [
                            'in' => 'path',
                            'name' => 'transition',
                            'required' => true,
                            'schema' => ['enum' => State::TRANSITIONS, 'type' => 'string']
                        ],
                        [
                            'in' => 'path',
                            'name' => 'workflow',
                            'required' => true,
                            'schema' => ['enum' => ['receipt'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite le la réception à son prochain statut de workflow'
                ],
                'path' => '/receipts/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')',
                'validate' => false
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:receipt', 'read:state'],
            'openapi_definition_name' => 'Receipt-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class Receipt extends EntityId implements MeasuredInterface {
    /** @var Collection<int, Check<I, Company|Component|ComponentFamily|Product|ProductFamily|Supplier>> */
    #[ORM\OneToMany(mappedBy: 'receipt', targetEntity: Check::class)]
    private Collection $checks;

    #[
        ApiProperty(description: 'Date', example: '2022-03-27'),
        ORM\Column(type: 'datetime_immutable'),
        Serializer\Groups(['read:receipt'])
    ]
    private DateTimeImmutable $date;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:receipt'])
    ]
    private State $embState;

    /** @var Item<I>|null */
    #[
        ApiProperty(description: 'Item', readableLink: false, example: '/api/purchase-order-items/1'),
        ORM\ManyToOne(inversedBy: 'receipts'),
        Serializer\Groups(['read:receipt'])
    ]
    private ?Item $item = null;

    #[
        ApiProperty(description: 'Quantité', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:receipt'])
    ]
    private Measure $quantity;

    /** @var Collection<int, Stock<I>> */
    #[ORM\ManyToMany(targetEntity: Stock::class, inversedBy: 'receipts')]
    private Collection $stocks;

    public function __construct() {
        $this->checks = new ArrayCollection();
        $this->date = new DateTimeImmutable();
        $this->embState = new State();
        $this->quantity = new Measure();
        $this->stocks = new ArrayCollection();
    }

    /**
     * @param Check<I, Company|Component|ComponentFamily|Product|ProductFamily|Supplier> $check
     *
     * @return $this
     */
    final public function addCheck(Check $check): self {
        if (!$this->checks->contains($check)) {
            $this->checks->add($check);
            $check->setReceipt($this);
        }
        return $this;
    }

    /**
     * @param Stock<I> $stock
     *
     * @return $this
     */
    final public function addStock(Stock $stock): self {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->addReceipt($this);
        }
        return $this;
    }

    final public function getBatchNumber(): string {
        return $this->date->format('Ymd');
    }

    /**
     * @return Collection<int, Check<I, Company|Component|ComponentFamily|Product|ProductFamily|Supplier>>
     */
    final public function getChecks(): Collection {
        return $this->checks;
    }

    final public function getDate(): DateTimeImmutable {
        return $this->date;
    }

    final public function getEmbState(): State {
        return $this->embState;
    }

    /**
     * @return Item<I>|null
     */
    final public function getItem(): ?Item {
        return $this->item;
    }

    final public function getMeasures(): array {
        return [$this->quantity];
    }

    final public function getQuantity(): Measure {
        return $this->quantity;
    }

    /**
     * @return I|null
     */
    final public function getReceiptItem() {
        return $this->item?->getItem();
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    /**
     * @return Collection<int, Stock<I>>
     */
    final public function getStocks(): Collection {
        return $this->stocks;
    }

    final public function getUnit(): ?Unit {
        return $this->item?->getUnit();
    }

    /**
     * @param Check<I, Company|Component|ComponentFamily|Product|ProductFamily|Supplier> $check
     *
     * @return $this
     */
    final public function removeCheck(Check $check): self {
        if ($this->checks->contains($check)) {
            $this->checks->removeElement($check);
            if ($check->getReceipt() === $this) {
                $check->setReceipt(null);
            }
        }
        return $this;
    }

    /**
     * @param Stock<I> $stock
     *
     * @return $this
     */
    final public function removeStock(Stock $stock): self {
        if ($this->stocks->contains($stock)) {
            $this->stocks->removeElement($stock);
            $stock->removeReceipt($this);
        }
        return $this;
    }

    /**
     * @return $this
     */
    final public function setDate(DateTimeImmutable $date): self {
        $this->date = $date;
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
     * @param Item<I>|null $item
     *
     * @return $this
     */
    final public function setItem(?Item $item): self {
        $this->item = $item;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setQuantity(Measure $quantity): self {
        $this->quantity = $quantity;
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
