<?php

namespace App\Entity\Project\Product;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Project\Product\CurrentPlace;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Interfaces\EmbeddedInterface;
use App\Entity\Logistics\Incoterms;
use App\Entity\Traits\BarCodeTrait;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\RefTrait;
use App\Entity\Traits\WorkflowTrait;
use App\Filter\RelationFilter;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: DateFilter::class, properties: ['expirationDate']),
    ApiFilter(filterClass: OrderFilter::class, properties: ['index', 'kind', 'ref']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['currentPlace' => 'name', 'family' => 'name']),
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'index' => 'partial',
        'kind' => 'partial',
        'ref' => 'partial'
    ]),
    ApiResource(
        description: 'Produit',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les produits',
                    'summary' => 'Récupère les produits'
                ],
                'normalization_context' => [
                    'groups' => ['read:current-place', 'read:measure', 'read:product:collection'],
                    'openapi_definition_name' => 'Product-collection'
                ]
            ],
            'post' => [
                'denormalization_context' => [
                    'groups' => ['create:product', 'write:measure'],
                    'openapi_definition_name' => 'Product-create'
                ],
                'openapi_context' => [
                    'description' => 'Créer un produit',
                    'summary' => 'Créer un produit'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'clone' => [
                'controller' => PlaceholderAction::class,
                'denormalization_context' => [
                    'groups' => ['write:product:clone'],
                    'openapi_definition_name' => 'Product-clone'
                ],
                'method' => 'POST',
                'openapi_context' => [
                    'description' => 'Clone un produit',
                    'summary' => 'Clone un produit',
                ],
                'path' => '/products/{id}/clone',
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')'
            ],
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un produit',
                    'summary' => 'Supprime un produit'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un produit',
                    'summary' => 'Récupère un produit'
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un produit',
                    'summary' => 'Modifie un produit'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')'
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'method' => 'PATCH',
                'path' => '/products/{id}/promote/{transition}',
                'openapi_context' => [
                    'description' => 'Transite le produit à son prochain statut de workflow',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'transition',
                        'required' => true,
                        'schema' => [
                            'enum' => CurrentPlace::TRANSITIONS,
                            'type' => 'string'
                        ]
                    ]],
                    'requestBody' => ['content' => ['application/merge-patch+json' => []]],
                    'summary' => 'Transite le produit à son prochain statut de workflow'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')'
            ],
            'upgrade' => [
                'controller' => PlaceholderAction::class,
                'denormalization_context' => [
                    'groups' => ['write:product:clone'],
                    'openapi_definition_name' => 'Product-upgrade'
                ],
                'method' => 'POST',
                'path' => '/products/{id}/upgrade',
                'openapi_context' => [
                    'description' => 'Évolue le produit au prochain indice',
                    'summary' => 'Évolue le produit au prochain indice'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PROJECT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:product'],
            'openapi_definition_name' => 'Product-write'
        ],
        normalizationContext: [
            'groups' => ['read:current-place', 'read:measure', 'read:product'],
            'openapi_definition_name' => 'Product-read'
        ],
    ),
    ORM\Entity
]
class Product extends Entity implements BarCodeInterface, EmbeddedInterface {
    use BarCodeTrait;
    use NameTrait, RefTrait {
        RefTrait::__toString insteadof NameTrait;
    }
    use WorkflowTrait;

    public const KIND_EI = 'EI';
    public const KIND_PROTOTYPE = 'Prototype';
    public const KIND_SERIES = 'Série';
    public const KIND_SPARE = 'Pièce de rechange';
    public const PRODUCT_KINDS = [
        self::KIND_EI,
        self::KIND_PROTOTYPE,
        self::KIND_SERIES,
        self::KIND_SPARE,
    ];

    /** @var CurrentPlace */
    #[
        ApiProperty(description: 'Statut'),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:product', 'read:product:collection'])
    ]
    protected $currentPlace;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'HEATING WIRE (HSR25304)'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['create:product', 'read:product', 'write:product'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Référence', example: '54587F'),
        ORM\Column,
        Serializer\Groups(['create:product', 'read:product', 'read:product:collection', 'write:product', 'write:product:clone'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Temps auto'),
        ORM\Embedded
    ]
    private Measure $autoDuration;

    /** @var Collection<int, self> */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $children;

    #[
        ApiProperty(description: 'Temps auto chiffrage'),
        ORM\Embedded
    ]
    private Measure $costingAutoDuration;

    #[
        ApiProperty(description: 'Temps manu chiffrage'),
        ORM\Embedded
    ]
    private Measure $costingManualDuration;

    #[
        ApiProperty(description: 'Code douanier', required: false, example: '8544300089'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private ?string $customsCode;

    #[
        ApiProperty(description: 'Date d\'expiration', example: '2021-01-12'),
        Assert\DateTime,
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['create:product', 'read:product', 'read:product:collection', 'write:product'])
    ]
    private ?DateTimeImmutable $expirationDate;

    #[
        ApiProperty(description: 'Famille de produit', readableLink: false, example: '/api/product-families/1'),
        ORM\ManyToOne,
        Serializer\Groups(['create:product', 'read:product', 'read:product:collection', 'write:product'])
    ]
    private ?Family $family;

    #[
        ApiProperty(description: 'Volume prévisionnel'),
        ORM\Embedded,
        Serializer\Groups(['create:product', 'read:product', 'write:product'])
    ]
    private Measure $forecastVolume;

    #[
        ApiProperty(description: 'Incoterms', readableLink: false, required: true, example: '/api/incoterms/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private ?Incoterms $incoterms;

    #[
        ApiProperty(description: 'Indice', required: false, example: '02'),
        Assert\Length(max: 255),
        ORM\Column(name: '`index`'),
        Serializer\Groups(['create:product', 'read:product', 'read:product:collection', 'write:product', 'write:product:clone'])
    ]
    private ?string $index = null;

    #[
        ApiProperty(description: 'Indice interne', required: true, example: 1),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(type: 'smallint', options: ['default' => 1, 'unsigned' => true]),
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private int $internalIndex = 1;

    #[
        ApiProperty(description: 'Type', example: self::KIND_PROTOTYPE, openapiContext: ['enum' => self::PRODUCT_KINDS]),
        Assert\Choice(choices: self::PRODUCT_KINDS),
        ORM\Column(options: ['default' => self::KIND_PROTOTYPE]),
        Serializer\Groups(['create:product', 'read:product', 'read:product:collection', 'write:product'])
    ]
    private ?string $kind = self::KIND_PROTOTYPE;

    #[
        ApiProperty(description: 'Gestion cuivre', required: false, example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private bool $managedCopper = false;

    #[
        ApiProperty(description: 'Temps manu'),
        ORM\Embedded
    ]
    private Measure $manualDuration;

    #[
        ApiProperty(description: 'Nombre max de prototypes', required: true),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private Measure $maxProto;

    #[
        ApiProperty(description: 'Délai de livraison minimum', required: true),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private Measure $minDelivery;

    #[
        ApiProperty(description: 'Production minimum', required: true),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private Measure $minProd;

    #[
        ApiProperty(description: 'Stock minimum', required: true),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private Measure $minStock;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Produit préféré des clients'),
        Assert\Length(max: 255),
        ORM\Column(nullable: true),
        Serializer\Groups(['create:product', 'read:product', 'write:product'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Conditionnement', required: true),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Embedded,
        Serializer\Groups(['create:product', 'read:product', 'write:product'])
    ]
    private Measure $packaging;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Type de packaging'),
        Assert\Length(max: 255),
        ORM\Column(nullable: true),
        Serializer\Groups(['create:product', 'read:product', 'write:product'])
    ]
    private ?string $packagingKind = null;

    #[
        ApiProperty(description: 'Unité parente', readableLink: false, example: '/api/products/3'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:product'])
    ]
    private ?self $parent = null;

    #[
        ApiProperty(description: 'Prix', required: true),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private Measure $price;

    #[
        ApiProperty(description: 'Prix sans cuivre', required: true),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private Measure $priceWithoutCopper;

    #[
        ApiProperty(description: 'Délai de production', required: true),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private Measure $productionDelay;

    #[
        ApiProperty(description: 'Prix de cession des composants', required: true),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private Measure $transfertPriceSupplies;

    #[
        ApiProperty(description: 'Prix de cession de main d\'œuvre', required: true),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private Measure $transfertPriceWork;

    #[
        ApiProperty(description: 'Poids'),
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private Measure $weight;

    final public function __construct() {
        $this->autoDuration = new Measure();
        $this->children = new ArrayCollection();
        $this->currentPlace = new CurrentPlace();
        $this->costingAutoDuration = new Measure();
        $this->costingManualDuration = new Measure();
        $this->forecastVolume = new Measure();
        $this->manualDuration = new Measure();
        $this->maxProto = new Measure();
        $this->minDelivery = new Measure();
        $this->minProd = new Measure();
        $this->minStock = new Measure();
        $this->packaging = new Measure();
        $this->price = new Measure();
        $this->priceWithoutCopper = new Measure();
        $this->productionDelay = new Measure();
        $this->transfertPriceSupplies = new Measure();
        $this->transfertPriceWork = new Measure();
        $this->weight = new Measure();
    }

    final public static function getBarCodeTableNumber(): string {
        return self::PRODUCT_BAR_CODE_TABLE_NUMBER;
    }

    final public function addChild(self $children): self {
        if (!$this->children->contains($children)) {
            $this->children->add($children);
            $children->setParent($this);
        }
        return $this;
    }

    final public function getAutoDuration(): Measure {
        return $this->autoDuration;
    }

    /**
     * @return Collection<int, self>
     */
    final public function getChildren(): Collection {
        return $this->children;
    }

    final public function getCostingAutoDuration(): Measure {
        return $this->costingAutoDuration;
    }

    final public function getCostingManualDuration(): Measure {
        return $this->costingManualDuration;
    }

    final public function getCustomsCode(): ?string {
        return $this->customsCode;
    }

    final public function getEmbeddedMeasures(): array {
        return [
            $this->autoDuration,
            $this->costingAutoDuration,
            $this->costingManualDuration,
            $this->forecastVolume,
            $this->manualDuration,
            $this->maxProto,
            $this->minDelivery,
            $this->minProd,
            $this->minStock,
            $this->packaging,
            $this->price,
            $this->priceWithoutCopper,
            $this->productionDelay,
            $this->transfertPriceSupplies,
            $this->transfertPriceWork,
            $this->weight
        ];
    }

    final public function getExpirationDate(): ?DateTimeImmutable {
        return $this->expirationDate;
    }

    final public function getFamily(): ?Family {
        return $this->family;
    }

    final public function getForecastVolume(): Measure {
        return $this->forecastVolume;
    }

    final public function getIncoterms(): ?Incoterms {
        return $this->incoterms;
    }

    final public function getIndex(): ?string {
        return $this->index;
    }

    final public function getInternalIndex(): int {
        return $this->internalIndex;
    }

    final public function getKind(): ?string {
        return $this->kind;
    }

    final public function getManualDuration(): Measure {
        return $this->manualDuration;
    }

    final public function getMaxProto(): Measure {
        return $this->maxProto;
    }

    final public function getMinDelivery(): Measure {
        return $this->minDelivery;
    }

    final public function getMinProd(): Measure {
        return $this->minProd;
    }

    final public function getMinStock(): Measure {
        return $this->minStock;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getPackaging(): Measure {
        return $this->packaging;
    }

    final public function getPackagingKind(): ?string {
        return $this->packagingKind;
    }

    final public function getParent(): ?self {
        return $this->parent;
    }

    final public function getPrice(): Measure {
        return $this->price;
    }

    final public function getPriceWithoutCopper(): Measure {
        return $this->priceWithoutCopper;
    }

    final public function getProductionDelay(): Measure {
        return $this->productionDelay;
    }

    final public function getTransfertPriceSupplies(): Measure {
        return $this->transfertPriceSupplies;
    }

    final public function getTransfertPriceWork(): Measure {
        return $this->transfertPriceWork;
    }

    final public function getWeight(): Measure {
        return $this->weight;
    }

    final public function isManagedCopper(): bool {
        return $this->managedCopper;
    }

    final public function removeChild(self $children): self {
        if ($this->children->contains($children)) {
            $this->children->removeElement($children);
            if ($children->getParent() === $this) {
                $children->setParent(null);
            }
        }
        return $this;
    }

    final public function setAutoDuration(Measure $autoDuration): self {
        $this->autoDuration = $autoDuration;
        return $this;
    }

    final public function setCostingAutoDuration(Measure $costingAutoDuration): self {
        $this->costingAutoDuration = $costingAutoDuration;
        return $this;
    }

    final public function setCostingManualDuration(Measure $costingManualDuration): self {
        $this->costingManualDuration = $costingManualDuration;
        return $this;
    }

    final public function setCustomsCode(?string $customsCode): self {
        $this->customsCode = $customsCode;
        return $this;
    }

    final public function setExpirationDate(?DateTimeImmutable $expirationDate): self {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    final public function setFamily(?Family $family): self {
        $this->family = $family;
        return $this;
    }

    final public function setForecastVolume(Measure $forecastVolume): self {
        $this->forecastVolume = $forecastVolume;
        return $this;
    }

    final public function setIncoterms(?Incoterms $incoterms): self {
        $this->incoterms = $incoterms;
        return $this;
    }

    final public function setIndex(?string $index): self {
        $this->index = $index;
        return $this;
    }

    final public function setInternalIndex(int $internalIndex): self {
        $this->internalIndex = $internalIndex;
        return $this;
    }

    final public function setKind(?string $kind): self {
        $this->kind = $kind;
        return $this;
    }

    final public function setManagedCopper(bool $managedCopper): self {
        $this->managedCopper = $managedCopper;
        return $this;
    }

    final public function setManualDuration(Measure $manualDuration): self {
        $this->manualDuration = $manualDuration;
        return $this;
    }

    final public function setMaxProto(Measure $maxProto): self {
        $this->maxProto = $maxProto;
        return $this;
    }

    final public function setMinDelivery(Measure $minDelivery): self {
        $this->minDelivery = $minDelivery;
        return $this;
    }

    final public function setMinProd(Measure $minProd): self {
        $this->minProd = $minProd;
        return $this;
    }

    final public function setMinStock(Measure $minStock): self {
        $this->minStock = $minStock;
        return $this;
    }

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }

    final public function setPackaging(Measure $packaging): self {
        $this->packaging = $packaging;
        return $this;
    }

    final public function setPackagingKind(?string $packagingKind): self {
        $this->packagingKind = $packagingKind;
        return $this;
    }

    final public function setParent(?self $parent): self {
        $this->parent = $parent;
        return $this;
    }

    final public function setPrice(Measure $price): self {
        $this->price = $price;
        return $this;
    }

    final public function setPriceWithoutCopper(Measure $priceWithoutCopper): self {
        $this->priceWithoutCopper = $priceWithoutCopper;
        return $this;
    }

    final public function setProductionDelay(Measure $productionDelay): self {
        $this->productionDelay = $productionDelay;
        return $this;
    }

    final public function setTransfertPriceSupplies(Measure $transfertPriceSupplies): self {
        $this->transfertPriceSupplies = $transfertPriceSupplies;
        return $this;
    }

    final public function setTransfertPriceWork(Measure $transfertPriceWork): self {
        $this->transfertPriceWork = $transfertPriceWork;
        return $this;
    }

    final public function setWeight(Measure $weight): self {
        $this->weight = $weight;
        return $this;
    }
}
