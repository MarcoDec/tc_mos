<?php

namespace App\Entity\Logistics\Order;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Logistics\Order\State;
use App\Entity\Embeddable\Blocker;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Management\Unit;
use App\Entity\Project\Product\Family as ProductFamily;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Component\Family as ComponentFamily;
use App\Entity\Purchase\Order\Order;
use App\Entity\Purchase\Supplier\Supplier;
use App\Filter\SetFilter;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Filter\RelationFilter;
use App\Filter\CustomGetterFilter;

/**
 * @template I of \App\Entity\Purchase\Component\Component|\App\Entity\Project\Product\Product
 */
#[
    ApiFilter(filterClass: SetFilter::class, properties: ['embState.state' => 'partial', 'embBlocker.state' => 'partial']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['date' => 'exact']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['stocks']),
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
            'get',
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
                            'schema' => ['enum' => [...State::TRANSITIONS, ...Blocker::TRANSITIONS], 'type' => 'string']
                        ],
                        [
                            'in' => 'path',
                            'name' => 'workflow',
                            'required' => true,
                            'schema' => ['enum' => ['receipt','blocker'], 'type' => 'string']
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
class Receipt extends Entity {

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
    private Blocker $embBlocker;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:receipt'])
    ]
    private State $embState;

    #[
        ApiProperty(description: 'Quantité réceptionnée', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:receipt'])
    ]
    private Measure $quantity;

    /** @var Collection<int, Stock<I>> */
    #[ORM\ManyToMany(targetEntity: Stock::class, mappedBy: 'receipts')]
    private Collection $stocks;

    public function __construct() {
        $this->date = new DateTimeImmutable();
        $this->embBlocker = new Blocker();
        $this->embState = new State();
        $this->quantity = new Measure();
        $this->stocks = new ArrayCollection();
    }
    #[
        ApiProperty(description: 'Nom complet', example: 'Roosevelt Super'),
        Serializer\Groups(['read:employee', 'read:user', 'read:employee:collection'])
    ]
    final public function getGetterFilter(): string {
        return $this->getPurchaseOrder()?->getId();
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

    final public function getDate(): DateTimeImmutable {
        return $this->date;
    }

    final public function getEmbBlocker(): Blocker {
        return $this->embBlocker;
    }

    final public function getEmbState(): State {
        return $this->embState;
    }

    final public function getMeasures(): array {
        return [$this->quantity];
    }

    final public function getUnitMeasures(): array {
        return [$this->quantity];
    }
    final public function getCurrencyMeasures(): array {
        return [];
    }

    final public function getQuantity(): Measure {
        return $this->quantity;
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
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
    final public function setEmbBlocker(Blocker $embBlocker): self {
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
    final public function setQuantity(Measure $quantity): self {
        $this->quantity = $quantity;
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
    final public function setState(string $state): self {
        $this->embState->setState($state);
        return $this;
    }
}
