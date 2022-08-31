<?php

namespace App\Entity\Accounting;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\ItemType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Production\Manufacturing\Expedition;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template I of \App\Entity\Purchase\Component\Component|\App\Entity\Project\Product\Product
 */
#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['bill']),
    ApiResource(
        description: 'Lignes facturée',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les lignes',
                    'summary' => 'Récupère les lignes',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une ligne',
                    'summary' => 'Supprime un une ligne',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une ligne',
                    'summary' => 'Modifie une ligne',
                ]
            ]
        ],
        shortName: 'BillItem',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:item', 'write:measure'],
            'openapi_definition_name' => 'BillItem-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:measure'],
            'openapi_definition_name' => 'BillItem-read',
            'skip_null_values' => false
        ],
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'item'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'bill_item')
]
abstract class Item extends Entity {
    final public const TYPES = [ItemType::TYPE_COMPONENT => ComponentItem::class, ItemType::TYPE_PRODUCT => ProductItem::class];

    #[
        ApiProperty(description: 'Facture', readableLink: false, example: '/api/bills/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected ?Bill $bill = null;

    /** @var Expedition<I>|null */
    #[
        ApiProperty(description: 'Commande', readableLink: false, example: '/api/expeditions/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected ?Expedition $expedition = null;

    /** @var I|null */
    protected $item;

    #[
        ApiProperty(description: 'Notes', example: 'Lorem ipsum'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected ?string $notes = null;

    #[
        ApiProperty(description: 'Prix', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected Measure $price;

    #[
        ApiProperty(description: 'Quantité', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected Measure $quantity;

    #[
        ApiProperty(description: 'Référence', example: 'TSKD'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Poids', openapiContext: ['$ref' => '#/components/schemas/Measure-mass']),
        ORM\Embedded,
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected Measure $weight;

    public function __construct() {
        $this->price = new Measure();
        $this->quantity = new Measure();
        $this->weight = new Measure();
    }

    final public function getBill(): ?Bill {
        return $this->bill;
    }

    /**
     * @return Expedition<I>|null
     */
    final public function getExpedition(): ?Expedition {
        return $this->expedition;
    }

    /**
     * @return I|null
     */
    final public function getItem() {
        return $this->item;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getPrice(): Measure {
        return $this->price;
    }

    final public function getQuantity(): Measure {
        return $this->quantity;
    }

    final public function getRef(): ?string {
        return $this->ref;
    }

    final public function getWeight(): Measure {
        return $this->weight;
    }

    /**
     * @return $this
     */
    final public function setBill(?Bill $bill): self {
        $this->bill = $bill;
        return $this;
    }

    /**
     * @param Expedition<I>|null $expedition
     *
     * @return $this
     */
    final public function setExpedition(?Expedition $expedition): self {
        $this->expedition = $expedition;
        return $this;
    }

    /**
     * @param I $item
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
     * @return $this
     */
    final public function setPrice(Measure $price): self {
        $this->price = $price;
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
    final public function setRef(?string $ref): self {
        $this->ref = $ref;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setWeight(Measure $weight): self {
        $this->weight = $weight;
        return $this;
    }
}
