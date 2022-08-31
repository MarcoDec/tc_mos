<?php

namespace App\Entity\Logistics\Stock;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\ItemType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Logistics\Warehouse;
use App\Entity\Management\Unit;
use App\Entity\Traits\BarCodeTrait;
use App\Filter\RelationFilter;
use App\Repository\Logistics\Stock\StockRepository;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template T of MeasuredInterface
 */
#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['warehouse']),
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
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un stock',
                    'summary' => 'Modifie un stock',
                ]
            ],
            'out' => [
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Sortie d\'un stock',
                    'requestBody' => [
                        'content' => [
                            'application/merge-patch+json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Measure-unitary'
                                ]
                            ]
                        ]
                    ],
                    'summary' => 'Sortie d\'un stock',
                ],
                'path' => '/stocks/{id}/out'
            ],
            'transfer' => [
                'controller' => PlaceholderAction::class,
                'denormalization_context' => [
                    'groups' => ['transfer:stock', 'write:measure'],
                    'openapi_definition_name' => 'Product-transfer'
                ],
                'method' => 'POST',
                'openapi_context' => [
                    'description' => 'Transfert un stock',
                    'summary' => 'Transfert un stock',
                ],
                'path' => '/transfer/{id}/out'
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
    ORM\DiscriminatorColumn(name: 'type', type: 'item'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity(repositoryClass: StockRepository::class),
    ORM\InheritanceType('SINGLE_TABLE')
]
abstract class Stock extends Entity implements BarCodeInterface, MeasuredInterface {
    use BarCodeTrait;

    final public const TYPES = [
        ItemType::TYPE_COMPONENT => ComponentStock::class,
        ItemType::TYPE_PRODUCT => ProductStock::class
    ];

    #[
        ApiProperty(description: 'Numéro de lot', example: '165486543'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:stock', 'write:stock'])
    ]
    protected ?string $batchNumber = null;

    /**
     * @var null|T
     */
    #[
        ApiProperty(description: 'Élément', readableLink: false, example: '/api/components/1'),
        Serializer\Groups(['read:stock'])
    ]
    protected $item;

    #[
        ApiProperty(description: 'Enfermé ?', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:stock', 'write:stock'])
    ]
    protected bool $jail = false;

    #[
        ApiProperty(description: 'Localisation', example: 'Rayon B'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:stock', 'write:stock'])
    ]
    protected ?string $location = null;

    #[
        ApiProperty(description: 'Quantité', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        AppAssert\Measure,
        ORM\Embedded,
        Serializer\Groups(['read:stock', 'transfer:stock', 'write:stock'])
    ]
    protected Measure $quantity;

    #[
        ApiProperty(description: 'Entrepôt', readableLink: false, example: '/api/warehouses/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:stock', 'transfer:stock', 'write:stock'])
    ]
    protected ?Warehouse $warehouse = null;

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

    public function getMeasures(): array {
        return [$this->quantity];
    }

    final public function getQuantity(): Measure {
        return $this->quantity;
    }

    public function getUnit(): ?Unit {
        return $this->item?->getUnit();
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

    /**
     * @return $this
     */
    final public function substract(Measure $quantity): self {
        $this->quantity = $this->quantity->substract($quantity);
        return $this;
    }
}
