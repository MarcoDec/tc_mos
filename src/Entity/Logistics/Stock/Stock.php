<?php

namespace App\Entity\Logistics\Stock;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Logistics\Warehouse;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Traits\BarCodeTrait;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'location' => 'partial',
    ]),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'warehouse' => 'name',
    ]),
    ApiResource(
        description: 'Stock',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les stocks',
                    'summary' => 'Récupère les stocks',
                ]
            ]
        ],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un stock',
                    'summary' => 'Récupère un stock',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un stock',
                    'summary' => 'Modifie un stock',
                ]
            ],
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un stock',
                    'summary' => 'Supprime un stock',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:stock', 'write:warehouse', 'write:measure', 'write:name', 'write:unit', 'write:company'],
            'openapi_definition_name' => 'Stock-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:stock', 'read:warehouse', 'read:measure', 'read:name', 'read:unit', 'read:company'],
            'openapi_definition_name' => 'Stock-read'
        ],
    ),
    ORM\Entity,
    ORM\DiscriminatorColumn(name: 'type', type: 'string'),
    ORM\DiscriminatorMap(Stock::TYPES)
]
abstract class Stock extends Entity implements BarCodeInterface {
    use BarCodeTrait;

    public const TYPES = ['component' => ComponentStock::class, 'product' => ProductStock::class];

    #[
        ApiProperty(description: 'Numéro de lot', example: '165486543'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:stock', 'write:stock'])
    ]
    private ?string $batchNumber = null;

    /**
     * @var Component|null|Product
     */
    private $item;

    #[
        ApiProperty(description: 'Enfermé ?', required: true, example: false),
        ORM\Column(type: 'boolean', options: ['default' => false]),
        Serializer\Groups(['read:stock', 'write:stock'])
    ]
    private bool $jail = false;

    #[
        ApiProperty(description: 'Localisation', example: 'Rayon B'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:stock', 'write:stock'])
    ]
    private ?string $location = null;

    #[
        ApiProperty(description: 'Quantité', example: '54'),
        Assert\NotBlank(groups: ['Default', 'Receipt']),
        Assert\Positive(groups: ['Default', 'Receipt']),
        ORM\Embedded(Measure::class),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private Measure $quantity;

    #[
        ApiProperty(description: 'Entrepôt', readableLink: false, example: ['/api/warehouses/1', '/api/warehouses/2']),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Warehouse::class),
        Serializer\Groups(['read:warehouse', 'write:warehouse'])
    ]
    private ?Warehouse $warehouse;

    public function __construct() {
        $this->quantity = new Measure();
    }

    public static function getBarCodeTableNumber(): string {
        return self::STOCK_BAR_CODE_PREFIX;
    }

    abstract public function getItem(): Product|Component|null;

    abstract public function getItemType(): string;

    public function getBatchNumber(): ?string {
        return $this->batchNumber;
    }

    public function getJail(): ?bool {
        return $this->jail;
    }

    public function getLocation(): ?string {
        return $this->location;
    }

    public function getQuantity(): Measure {
        return $this->quantity;
    }

    public function getWarehouse(): ?Warehouse {
        return $this->warehouse;
    }

    public function setBatchNumber(?string $batchNumber): self {
        $this->batchNumber = $batchNumber;

        return $this;
    }

    public function setJail(bool $jail): self {
        $this->jail = $jail;

        return $this;
    }

    public function setLocation(?string $location): self {
        $this->location = $location;

        return $this;
    }

    public function setQuantity(Measure $quantity): self {
        $this->quantity = $quantity;

        return $this;
    }

    public function setWarehouse(?Warehouse $warehouse): self {
        $this->warehouse = $warehouse;

        return $this;
    }
}
