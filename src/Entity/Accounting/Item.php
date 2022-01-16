<?php

namespace App\Entity\Accounting;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Production\Manufacturing\Expedition;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Traits\NotesTrait;
use App\Entity\Traits\RefTrait;
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
                'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_ADMIN.'\')'
            ]

        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_READER.'\')'
        ],
        shortName: 'AccountingItem',
        denormalizationContext: [
            'groups' => ['write:measure', 'write:unit', 'write:notes', 'write:ref', 'write:item'],
            'openapi_definition_name' => 'AccountingItem-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:notes', 'read:ref', 'read:item'],
            'openapi_definition_name' => 'AccountingItem-read'
        ],
    ),
    ORM\Entity,
    ORM\DiscriminatorColumn(name: 'type', type: 'string'),
    ORM\DiscriminatorMap(Item::TYPES),
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'bill_item')
]
abstract class Item extends Entity {
    use NotesTrait;
    use RefTrait;

    public const TYPES = ['component' => ComponentItem::class, 'product' => ProductItem::class];

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

    #[
        ApiProperty(description: 'Référence', required: false, example: 'TSKD'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:ref', 'write:ref'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Facture', required: false, example: '/api/bills/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Bill::class),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private ?Bill $bill = null;

    #[
        ApiProperty(description: 'Commande', required: false, example: '/api/expeditions/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Expedition::class),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private ?Expedition $expedition = null;

    #[
        ApiProperty(description: 'Prix', required: true, example: 0),
        ORM\Column(type: 'float', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private float $price = 0;

    #[
        ApiProperty(description: 'Quantité', example: '54'),
        ORM\Embedded(Measure::class),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private Measure $quantity;

    #[
        ApiProperty(description: 'Poids', example: '54'),
        ORM\Embedded(Measure::class),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private Measure $weight;

    public function __construct() {
        $this->quantity = new Measure();
        $this->weight = new Measure();
    }

    final public function getBill(): ?Bill {
        return $this->bill;
    }

    final public function getExpedition(): ?Expedition {
        return $this->expedition;
    }

    /**
     * @return Component|null|Product
     */
    final public function getItem() {
        return $this->item;
    }

    final public function getPrice(): float {
        return $this->price;
    }

    final public function getQuantity(): Measure {
        return $this->quantity;
    }

    final public function getWeight(): Measure {
        return $this->weight;
    }

    final public function setBill(?Bill $bill): self {
        $this->bill = $bill;
        return $this;
    }

    final public function setExpedition(?Expedition $expedition): self {
        $this->expedition = $expedition;
        return $this;
    }

    /**
     * @param Component|null|Product $item
     */
    final public function setItem($item): self {
        $this->item = $item;
        return $this;
    }

    final public function setPrice(float $price): self {
        $this->price = $price;
        return $this;
    }

    final public function setQuantity(Measure $quantity): self {
        $this->quantity = $quantity;
        return $this;
    }

    final public function setWeight(Measure $weight): self {
        $this->weight = $weight;
        return $this;
    }
}
