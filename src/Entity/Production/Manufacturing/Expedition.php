<?php

namespace App\Entity\Production\Manufacturing;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Selling\Order\ProductItem;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Filter\RelationFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


/**
 * @template I of \App\Entity\Purchase\Component\Component|\App\Entity\Project\Product\Product
 */
#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['note.date', 'note.bill.dueDate']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['item.item.customer.id' => 'partial', 'note.embState.state' => 'partial', 'note.freightSurcharge.code' => 'partial', 'note.freightSurcharge.value' => 'partial', 'note.nonBillable' => 'partial', 'note.date' => 'partial', 'note.ref' => 'partial'
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
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une expédition',
                    'summary' => 'Supprime une expédition',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une expédition',
                    'summary' => 'Modifie une expédition',
                ]
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:expedition', 'write:measure'],
            'openapi_definition_name' => 'Expedition-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:expedition', 'read:measure'],
            'openapi_definition_name' => 'Expedition-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity
]
class Expedition extends Entity {
    #[
        ApiProperty(description: 'Numéro de lot', example: '165486543'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private ?string $batchNumber = null;

    #[
        ApiProperty(description: 'Date', example: '2022-03-24'),
        ORM\Column(type: 'date_immutable', nullable: false),
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private DateTimeImmutable $date;

    /** @var ProductItem<I>|null */
    #[
        ApiProperty(description: 'Item', readableLink: true),
        ORM\ManyToOne,
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private ?ProductItem $item = null;

    #[
        ApiProperty(description: 'Localisation', example: 'New York City'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private ?string $location = null;

    #[
        ApiProperty(description: 'Note de livraison', readableLink: true, example: '/api/delivery-notes/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private ?DeliveryNote $note = null;

    #[
        ApiProperty(description: 'Quantité', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private Measure $quantity;

    /** @var null|Stock<I> */
    #[
        ApiProperty(description: 'Item', readableLink: false, example: '/api/stocks/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private ?Stock $stock = null;

    public function __construct() {
        $this->date = new DateTimeImmutable();
        $this->quantity = new Measure();
    }

    final public function getBatchNumber(): ?string {
        return $this->batchNumber;
    }

    final public function getDate(): DateTimeImmutable {
        return $this->date;
    }

    /**
     * @return ProductItem<I>|null
     */
    final public function getItem(): ?ProductItem {
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

    /**
     * @return null|Stock<I>
     */
    final public function getStock(): ?Stock {
        return $this->stock;
    }

    /**
     * @return $this
     */
    final public function setBatchNumber(?string $batchNumber): self {
        $this->batchNumber = $batchNumber;
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
     * @param ProductItem<I>|null $item
     *
     * @return $this
     */
    final public function setItem(?ProductItem $item): self {
        $this->item = $item;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setLocation(?string $location): self {
        $this->location = $location;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setNote(?DeliveryNote $note): self {
        $this->note = $note;
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
     * @param null|Stock<I> $stock
     *
     * @return $this
     */
    final public function setStock(?Stock $stock): self {
        $this->stock = $stock;
        return $this;
    }
}
