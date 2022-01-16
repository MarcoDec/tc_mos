<?php

namespace App\Entity\Logistics\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Purchase\Order\Item;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Reçu',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les reçus',
                    'summary' => 'Récupère les reçus',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un reçu',
                    'summary' => 'Créer un reçu',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un reçu',
                    'summary' => 'Supprime un reçu',
                ]
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un reçu',
                    'summary' => 'Récupère un reçu',
                ],
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un reçu',
                    'summary' => 'Modifie un reçu',
                ]
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:receipt'],
            'openapi_definition_name' => 'Receipt-write'
        ],
        normalizationContext: [
            'groups' => ['read:receipt', 'read:id'],
            'openapi_definition_name' => 'Receipt-read'
        ]
    ),
    ORM\Entity(readOnly: true)
]
class Receipt extends Entity {
    #[
        ApiProperty(description: 'Item', required: false, example: '/api/purchase-order-items/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Item::class, inversedBy: 'receipts'),
        Serializer\Groups(['read:receipt', 'write:receipt'])
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
        Serializer\Groups(['read:receipt', 'write:receipt'])
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
