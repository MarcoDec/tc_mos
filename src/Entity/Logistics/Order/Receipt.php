<?php

namespace App\Entity\Logistics\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Entity;
use App\Entity\Purchase\Order\Item;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(readOnly: true)
]
class Receipt extends Entity {
    #[
        ApiProperty(description: 'Companie', required: false, example: '/api/purchase-order-items/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Item::class, inversedBy: 'receipts'),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    protected ?Item $item = null;

    #[
        ApiProperty(description: 'Date', required: false, example: '2022-27-03'),
        ORM\Column(type: 'datetime', nullable: true),
        Serializer\Groups(['read:receipt', 'write:receipt'])
    ]
    private ?DateTimeInterface $date = null;

    #[
        ApiProperty(description: 'Quantité', required: true, example: 0),
        ORM\Column(type: 'float', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private float $quantity = 0;

    final public function getDate(): ?DateTimeInterface {
        return $this->date;
    }

    final public function getItem(): ?Item {
        return $this->item;
    }

    final public function getQuantity(): float {
        return $this->quantity;
    }

    final public function setDate(?DateTimeInterface $date): self {
        $this->date = $date;

        return $this;
    }

    final public function setItem(?Item $item): self {
        $this->item = $item;

        return $this;
    }

    final public function setQuantity(float $quantity): self {
        $this->quantity = $quantity;

        return $this;
    }
}
