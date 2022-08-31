<?php

namespace App\Entity\Logistics\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Entity\Purchase\Order\Item;
use DateTimeImmutable;
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
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une réception',
                    'summary' => 'Créer une réception',
                ]
            ]
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:receipt'],
            'openapi_definition_name' => 'Receipt-write'
        ],
        normalizationContext: [
            'groups' => ['read:measure', 'read:receipt', 'read:id'],
            'openapi_definition_name' => 'Receipt-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity(readOnly: true)
]
class Receipt extends Entity implements MeasuredInterface {
    #[
        ApiProperty(description: 'Date', example: '2022-03-27'),
        ORM\Column(type: 'datetime_immutable', nullable: true),
        Serializer\Groups(['read:receipt', 'write:receipt'])
    ]
    private ?DateTimeImmutable $date = null;

    /** @var Item<I>|null */
    #[
        ApiProperty(description: 'Item', readableLink: false, example: '/api/purchase-order-items/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:receipt', 'write:receipt'])
    ]
    private ?Item $item = null;

    #[
        ApiProperty(description: 'Quantité', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:receipt', 'write:receipt'])
    ]
    private Measure $quantity;

    public function __construct() {
        $this->quantity = new Measure();
    }

    final public function getDate(): ?DateTimeImmutable {
        return $this->date;
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

    final public function getUnit(): ?Unit {
        return $this->item?->getUnit();
    }

    /**
     * @return $this
     */
    final public function setDate(?DateTimeImmutable $date): self {
        $this->date = $date;
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
}
