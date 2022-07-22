<?php

namespace App\Entity\Logistics\Stock;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Logistics\Warehouse;
use App\Entity\Traits\BarCodeTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @template T of object
 */
#[
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
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un stock',
                    'summary' => 'Supprime un stock',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
            ],
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
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:stock'],
            'openapi_definition_name' => 'Stock-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:stock'],
            'openapi_definition_name' => 'Stock-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\DiscriminatorColumn(name: 'type', type: 'string'),
    ORM\DiscriminatorMap(Stock::TYPES)
]
abstract class Stock extends Entity implements BarCodeInterface {
    use BarCodeTrait;

    public const TYPES = ['component' => ComponentStock::class, 'product' => ProductStock::class];

    /**
     * @var null|T
     */
    protected $item;

    #[
        ApiProperty(description: 'Numéro de lot', example: '165486543'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:stock', 'write:stock'])
    ]
    private ?string $batchNumber = null;

    #[
        ApiProperty(description: 'Enfermé ?', required: true, example: false),
        ORM\Column(options: ['default' => false]),
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
        ORM\Embedded,
        Serializer\Groups(['read:stock', 'write:stock'])
    ]
    private Measure $quantity;

    #[
        ApiProperty(description: 'Entrepôt', readableLink: false, example: ['/api/warehouses/1', '/api/warehouses/2']),
        ORM\ManyToOne,
        Serializer\Groups(['read:warehouse', 'write:warehouse'])
    ]
    private ?Warehouse $warehouse;

    public function __construct() {
        $this->quantity = new Measure();
    }

    public static function getBarCodeTableNumber(): string {
        return self::STOCK_BAR_CODE_PREFIX;
    }

    final public function getBatchNumber(): ?string {
        return $this->batchNumber;
    }

    /**
     * @return null|T
     */
    final public function getItem() {
        return $this->item;
    }

    final public function getLocation(): ?string {
        return $this->location;
    }

    final public function getQuantity(): Measure {
        return $this->quantity;
    }

    final public function getWarehouse(): ?Warehouse {
        return $this->warehouse;
    }

    final public function isJail(): bool {
        return $this->jail;
    }

    /**
     * @return $this
     */
    final public function setBatchNumber(?string $batchNumber): self {
        $this->batchNumber = $batchNumber;
        return $this;
    }

    /**
     * @param null|T $item
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
    final public function setJail(bool $jail): self {
        $this->jail = $jail;
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
    final public function setQuantity(Measure $quantity): self {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setWarehouse(?Warehouse $warehouse): self {
        $this->warehouse = $warehouse;
        return $this;
    }
}
