<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Embeddable\Measure;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Management\Unit;
use App\Validator as AppAssert;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

/**
 * @template I of Purchase\Component\Component|Project\Product\Product
 * @template O of Purchase\Order\Order|Selling\Order\Order
 */
#[ORM\MappedSuperclass]
abstract class Item extends EntityId implements MeasuredInterface {
    #[
        ApiProperty(description: 'Date de confirmation', example: '2022-03-24'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d']),
        Serializer\Groups(['read:item', 'write:item']),
    ]
    protected ?DateTimeImmutable $confirmedDate = null;

    #[
        ApiProperty(description: 'Quantité confirmée', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        AppAssert\Measure,
        ORM\Embedded,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected Measure $confirmedQuantity;

    /** @var I|null */
    protected $item;

    #[
        ApiProperty(description: 'Notes', example: 'Lorem ipsum'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected ?string $notes = null;

    /** @var null|O */
    protected $order;

    #[
        ApiProperty(description: 'Prix', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected Measure $price;

    #[
        ApiProperty(description: 'Référence', example: 'FIZ56'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Date de la demande', example: '2022-03-24'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d']),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected ?DateTimeImmutable $requestedDate = null;

    #[
        ApiProperty(description: 'Quantité demandée', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        AppAssert\Measure,
        ORM\Embedded,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected Measure $requestedQuantity;

    public function __construct() {
        $this->confirmedQuantity = new Measure();
        $this->price = new Measure();
        $this->requestedQuantity = new Measure();
    }

    final public function getCompany(): ?Company {
        return $this->order?->getCompany();
    }

    final public function getConfirmedDate(): ?DateTimeImmutable {
        return $this->confirmedDate;
    }

    final public function getConfirmedQuantity(): Measure {
        return $this->confirmedQuantity;
    }

    /**
     * @return I|null
     */
    final public function getItem() {
        return $this->item;
    }

    final public function getMeasures(): array {
        return [$this->confirmedQuantity, $this->price, $this->requestedQuantity];
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    /**
     * @return null|O
     */
    final public function getOrder() {
        return $this->order;
    }

    final public function getPrice(): Measure {
        return $this->price;
    }

    final public function getRef(): ?string {
        return $this->ref;
    }

    final public function getRequestedDate(): ?DateTimeImmutable {
        return $this->requestedDate;
    }

    final public function getRequestedQuantity(): Measure {
        return $this->requestedQuantity;
    }

    final public function getUnit(): ?Unit {
        return $this->item?->getUnit();
    }

    /**
     * @return $this
     */
    final public function setConfirmedDate(?DateTimeImmutable $confirmedDate): self {
        $this->confirmedDate = $confirmedDate;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setConfirmedQuantity(Measure $confirmedQuantity): self {
        $this->confirmedQuantity = $confirmedQuantity;
        return $this;
    }

    /**
     * @param I|null $item
     *
     * @return $this
     */
    final public function setItem($item): self {
        $this->item = $item;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @param null|O $order
     *
     * @return $this
     */
    final public function setOrder($order): self {
        $this->order = $order;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setPrice(Measure $price): self {
        $this->price = $price;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setRef(?string $ref): self {
        $this->ref = $ref;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setRequestedDate(?DateTimeImmutable $requestedDate): self {
        $this->requestedDate = $requestedDate;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setRequestedQuantity(Measure $requestedQuantity): self {
        $this->requestedQuantity = $requestedQuantity;
        return $this;
    }
}
