<?php

namespace App\Entity\Production\Manufacturing;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Selling\Order\Item;
use App\Filter\RelationFilter;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'note' => 'id',
    ]),
    ApiResource(
        description: 'Expédition',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les expéditions',
                    'summary' => 'Récupère les expéditions',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Crée une expédition',
                    'summary' => 'Crée une expédition',
                ]
            ],
        ],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une expédition',
                    'summary' => 'Récupère une expédition',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une expédition',
                    'summary' => 'Modifie une expédition',
                ]
            ],
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une expédition',
                    'summary' => 'Supprime une expédition',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:expedition', 'write:measure', 'write:unit'],
            'openapi_definition_name' => 'Stock-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:expedition', 'read:measure', 'read:unit'],
            'openapi_definition_name' => 'Stock-read'
        ],
    ),
    ORM\Entity
]
class Expedition extends Entity {
    #[
        ApiProperty(description: 'Item', required: false, example: '/api/selling-order-items/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Item::class),
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    protected ?Item $item = null;

    #[
        ApiProperty(description: 'Localisation', required: false, example: 'New York City'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    protected ?string $location = null;

    #[
        ApiProperty(description: 'Note de livraison', required: false, example: '/api/delivery-notes/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: DeliveryNote::class),
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    protected ?DeliveryNote $note = null;

    #[
        ApiProperty(description: 'Item', required: false, example: '/api/expéditions/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Stock::class),
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    protected ?Stock $stock = null;

    #[
        ApiProperty(description: 'Numéro de lot', example: '165486543'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private ?string $batchNumber = null;

    #[
        ApiProperty(description: 'Date', required: true, example: '2022-24-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: false),
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private DateTimeInterface $date;

    #[
        ApiProperty(description: 'Quantité', example: '54'),
        ORM\Embedded(Measure::class),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private Measure $quantity;

    public function __construct() {
        $this->date = new DateTime();
        $this->quantity = new Measure();
    }

    final public function getBatchNumber(): ?string {
        return $this->batchNumber;
    }

    final public function getDate(): DateTimeInterface {
        return $this->date;
    }

    final public function getItem(): ?Item {
        return $this->item;
    }

    final public function getLocation(): ?string {
        return $this->location;
    }

    final public function getNote(): ?DeliveryNote {
        return $this->note;
    }

    final public function getQuantity(): Measure {
        return $this->quantity;
    }

    final public function getStock(): ?Stock {
        return $this->stock;
    }

    final public function setBatchNumber(?string $batchNumber): self {
        $this->batchNumber = $batchNumber;
        return $this;
    }

    final public function setDate(DateTimeInterface $date): self {
        $this->date = $date;
        return $this;
    }

    final public function setItem(?Item $item): self {
        $this->item = $item;
        return $this;
    }

    final public function setLocation(?string $location): self {
        $this->location = $location;
        return $this;
    }

    final public function setNote(?DeliveryNote $note): self {
        $this->note = $note;
        return $this;
    }

    final public function setQuantity(Measure $quantity): self {
        $this->quantity = $quantity;
        return $this;
    }

    final public function setStock(?Stock $stock): self {
        $this->stock = $stock;
        return $this;
    }
}
