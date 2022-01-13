<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Order\Order as SupplierOrder;
use App\Entity\Selling\Order\Order as CustomerOrder;
use App\Entity\Traits\NotesTrait;
use App\Entity\Traits\RefTrait;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class Item extends Entity {
    use NotesTrait;
    use RefTrait;

    /**
     * @var Component|null|Product
     */
    protected $item;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Lorem ipsum'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:notes', 'write:notes'])
    ]
    protected ?string $notes = null;

    /**
     * @var CustomerOrder|null|SupplierOrder
     */
    protected $order;

    #[
        ApiProperty(description: 'Référence', required: false, example: 'FIZ56'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:ref', 'write:ref'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Date de confirmation', required: false, example: '2022-24-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private DateTimeInterface $confirmationDate;

    #[
        ApiProperty(description: 'Quantité confirmée', required: true, example: 0),
        ORM\Column(type: 'float', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private float $confirmedQuantity = 0;

    #[
        ApiProperty(description: 'Prix', required: true, example: 0),
        ORM\Column(type: 'float', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private float $price = 0;

    #[
        ApiProperty(description: 'Date de la demande', required: false, example: '2022-24-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private DateTimeInterface $requestedDate;

    #[
        ApiProperty(description: 'Quantité demandée', required: true, example: 0),
        ORM\Column(type: 'float', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private float $requestQuantity = 0;

    final public function getConfirmationDate(): ?DateTimeInterface {
        return $this->confirmationDate;
    }

    final public function getConfirmedQuantity(): float {
        return $this->confirmedQuantity;
    }

    /**
     * @return Component|null|Product
     */
    final public function getItem() {
        return $this->item;
    }

    /**
     * @return CustomerOrder|null|SupplierOrder
     */
    final public function getOrder() {
        return $this->order;
    }

    final public function getPrice(): float {
        return $this->price;
    }

    final public function getRequestedDate(): ?DateTimeInterface {
        return $this->requestedDate;
    }

    final public function getRequestQuantity(): float {
        return $this->requestQuantity;
    }

    final public function setConfirmationDate(?DateTimeInterface $confirmationDate): self {
        $this->confirmationDate = $confirmationDate;
        return $this;
    }

    final public function setConfirmedQuantity(float $confirmedQuantity): self {
        $this->confirmedQuantity = $confirmedQuantity;
        return $this;
    }

    /**
     * @param Component|null|Product $item
     */
    final public function setItem($item): self {
        $this->item = $item;
        return $this;
    }

    /**
     * @param CustomerOrder|null|SupplierOrder $order
     */
    final public function setOrder($order): self {
        $this->order = $order;
        return $this;
    }

    final public function setPrice(float $price): self {
        $this->price = $price;
        return $this;
    }

    final public function setRequestedDate(?DateTimeInterface $requestedDate): self {
        $this->requestedDate = $requestedDate;
        return $this;
    }

    final public function setRequestQuantity(float $requestQuantity): self {
        $this->requestQuantity = $requestQuantity;
        return $this;
    }
}
