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
use App\Filter\RelationFilter;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'ref' => 'partial',
        'kind' => 'partial',
        'index' => 'partial'
    ]),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'family' => 'name',
        'currentPlace' => 'name'
    ]),
    ApiFilter(filterClass: DateFilter::class, properties: ['expirationDate']),
    ApiFilter(filterClass: OrderFilter::class, properties: [
        'ref',
        'index',
        'kind'
    ]),
    ApiResource(
        description: 'Produit',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les produits',
                    'summary' => 'Récupère les produits',
                ],
                'normalization_context' => [
                    'groups' => ['read:product:collection', 'read:current_place']
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un produit',
                    'summary' => 'Créer un produit',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un produit',
                    'summary' => 'Supprime un produit',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un produit',
                    'summary' => 'Récupère un produit',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un produit',
                    'summary' => 'Modifie un produit',
                ],
                'denormalization_context' => [
                    'groups' => ['patch:product']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ],
            'clone' => [
                'method' => 'POST',
                'path' => '/products/{id}/clone',
                'controller' => PlaceholderAction::class,
                'openapi_context' => [
                    'description' => 'Clone un produit',
                    'summary' => 'Clone un produit',
                ],
                'denormalization_context' => [
                    'groups' => ['write:product:clone']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ],
            'upgrade' => [
                'method' => 'POST',
                'path' => '/products/{id}/upgrade',
                'controller' => PlaceholderAction::class,
                'openapi_context' => [
                    'description' => 'Dupliquer un produit en le reliant à son parent',
                    'summary' => 'Dupliquer un produit en le reliant à son parent',
                ],
                'denormalization_context' => [
                    'groups' => ['write:product:upgrade']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ],
            'promote' => [
                'method' => 'PATCH',
                'path' => '/products/{id}/promote',
                'controller' => PlaceholderAction::class,
                'openapi_context' => [
                    'description' => 'Passer un produit à un nouveau statut',
                    'summary' => 'Passer un produit à un nouveau statut',
                ],
                'denormalization_context' => [
                    'groups' => ['write:product:promote']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PROJECT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:product', 'write:name', 'write:family', 'write:incoterms', 'write:measure', 'write:current_place', 'write:ref'],
            'openapi_definition_name' => 'Product-write'
        ],
        normalizationContext: [
            'groups' => ['read:product', 'read:id', 'read:name', 'read:family', 'read:incoterms', 'read:measure', 'read:current_place', 'read:ref'],
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

    #[
        ApiProperty(description: 'Temps auto', example: '7'),
        ORM\Embedded(Measure::class)
    ]
    protected Measure $autoDuration;

    #[
        ApiProperty(description: 'Statut'),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:current_place', 'read:product:collection'])
    ]
    protected CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Place', required: true, example: 'disabled'),
    ]
    protected ?string $currentPlaceName = null;

    #[
        ApiProperty(description: 'Volume prévisionnel', example: '2000'),
        ORM\Embedded(Measure::class),
        Serializer\Groups(['write:measure'])
    ]
    protected Measure $forecastVolume;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'HEATING WIRE (HSR25304)'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name', 'write:name', 'patch:product'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Référence', example: '54587F'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:ref', 'write:ref', 'read:product:collection', 'patch:product', 'write:product:clone', 'write:product:upgrade'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Poids', example: '100'),
        ORM\Embedded(Measure::class)
    ]
    protected Measure $weight;

    /**
     * @var Collection<int, self>
     */
    #[
        ApiProperty(description: 'Produits enfant', required: false, readableLink: false, example: ['/api/products/5', '/api/products/14']),
        ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['persist', 'remove']),
        Serializer\Groups(['read:product'])
    ]
    private Collection $children;

    #[
        ApiProperty(description: 'Code douanier', required: false, example: '8544300089'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:product'])
    ]
    private ?string $customsCode;

    #[
        ApiProperty(description: 'Date d\'expiration', example: '2021-01-12 10:39:37'),
        Assert\DateTime,
        ORM\Column(type: 'datetime', nullable: true),
        Serializer\Groups(['read:product', 'write:product', 'read:product:collection'])
    ]
    private ?DateTimeInterface $expirationDate;

    #[
        ApiProperty(description: 'Famille de produit', readableLink: false, example: '/api/product-families/5'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Family::class),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    private ?Family $family;

    #[
        ApiProperty(description: 'Incoterms', required: true, readableLink: false, example: '/api/incoterms/2'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Incoterms::class),
        Serializer\Groups(['read:incoterms'])
    ]
    private ?Incoterms $incoterms;

    #[
        ApiProperty(description: 'Indice', required: false, example: '02'),
        Assert\Length(max: 255),
        ORM\Column(type: 'string', length: 255, nullable: true, name: 'product_index'),
        Serializer\Groups(['read:product', 'write:product', 'patch:product', 'read:product:collection', 'write:product:clone', 'write:product:upgrade'])
    ]
    private ?string $index = null;

    #[
        ApiProperty(description: 'Index interne', required: true, example: '1'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 1, 'unsigned' => true], type: 'smallint'),
        Serializer\Groups(['read:product'])
    ]
    private int $internalIndex = 1;

    #[
        ApiProperty(description: 'Type', example: 'Série', openapiContext: ['enum' => self::PRODUCT_KINDS]),
        Assert\Choice(choices: self::PRODUCT_KINDS),
        ORM\Column(options: ['default' => self::KIND_PROTOTYPE], type: 'string', length: 255),
        Serializer\Groups(['read:product', 'write:product', 'read:product:collection', 'patch:product'])
    ]
    private ?string $kind = self::KIND_PROTOTYPE;

    #[
        ApiProperty(description: 'Suivre le cuivre', required: false, example: true),
        ORM\Column(options: ['default' => false], type: 'boolean'),
        Serializer\Groups(['read:product'])
    ]
    private bool $managedCopper = false;

    #[
        ApiProperty(description: 'Prototype maximum', required: true, example: '3'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 3, 'unsigned' => true], type: 'smallint'),
        Serializer\Groups(['read:product'])
    ]
    private int $maxProto = 3;

    #[
        ApiProperty(description: 'Livraison minimum', required: true, example: '10'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 10, 'unsigned' => true], type: 'smallint'),
        Serializer\Groups(['read:product'])
    ]
    private int $minDelivery = 10;

    #[
        ApiProperty(description: 'Production minimum', required: true, example: '10'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 10, 'unsigned' => true], type: 'smallint'),
        Serializer\Groups(['read:product'])
    ]
    private int $minProd = 10;

    #[
        ApiProperty(description: 'Stock minimum', required: true, example: '12'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'integer'),
        Serializer\Groups(['read:product'])
    ]
    private int $minStock = 0;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Produit préféré des clients'),
        Assert\Length(max: 255),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Conditionnement', required: true, example: '1'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 1, 'unsigned' => true], type: 'integer'),
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private int $packaging = 1;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Type de packaging'),
        Assert\Length(max: 255),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private ?string $packagingKind = null;

    #[
        ApiProperty(description: 'Unité parente', readableLink: false, example: '/api/products/3'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:product'])
    ]
    private ?self $parent = null;

    #[
        ApiProperty(description: 'Nouveau statut', required: false, example: 'draft'),
        Serializer\Groups(['write:product:promote'])
    ]
    private ?string $place = null;

    #[
        ApiProperty(description: 'Prix', required: true, example: '4.2'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'float'),
        Serializer\Groups(['read:product'])
    ]
    private float $price = 0;

    #[
        ApiProperty(description: 'Prix sans cuivre', required: true, example: '3.15'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'float'),
        Serializer\Groups(['read:product'])
    ]
    private float $priceWithoutCopper = 0;

    #[
        ApiProperty(description: 'Délai de production', required: true, example: '7'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'smallint'),
        Serializer\Groups(['read:product'])
    ]
    private int $productionDelay = 0;

    #[
        ApiProperty(description: 'Prix de cession des composants', required: true, example: '1.2'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'float'),
        Serializer\Groups(['read:product'])
    ]
    private float $transfertPriceSupplies = 0;

    #[
        ApiProperty(description: 'Prix de cession de main d\'œuvre', required: true, example: '11.2'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'float'),
        Serializer\Groups(['read:product'])
    ]
    private float $transfertPriceWork = 0;

    public function __construct() {
        $this->children = new ArrayCollection();
        $this->currentPlace = new CurrentPlace();
        $this->weight = new Measure();
        $this->forecastVolume = new Measure();
        $this->autoDuration = new Measure();
    }

    public static function getBarCodeTableNumber(): string {
        return self::PRODUCT_BAR_CODE_TABLE_NUMBER;
    }

    public function addChild(self $child): self {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection {
        return $this->children;
    }

    public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    public function getCurrentPlaceName(): ?string {
        return $this->currentPlace->getName() ?? null;
    }

    public function getCustomsCode(): ?string {
        return $this->customsCode;
    }

    public function getEmbeddedMeasures(): array {
        $measures = [];

        /** @phpstan-ignore-next-line */
        foreach ($this as $key => $value) {
            if ($value instanceof Measure) {
                $measures[$key] = $value;
            }
        }

        return $measures;
    }

    public function getExpirationDate(): ?DateTimeInterface {
        return $this->expirationDate;
    }

    public function getFamily(): ?Family {
        return $this->family;
    }

    public function getForecastVolume(): Measure {
        return $this->forecastVolume;
    }

    public function getIncoterms(): ?Incoterms {
        return $this->incoterms;
    }

    public function getIndex(): ?string {
        return $this->index;
    }

    public function getInternalIndex(): ?int {
        return $this->internalIndex;
    }

    public function getKind(): ?string {
        return $this->kind;
    }

    public function getManagedCopper(): ?bool {
        return $this->managedCopper;
    }

    public function getMaxProto(): ?int {
        return $this->maxProto;
    }

    public function getMinDelivery(): ?int {
        return $this->minDelivery;
    }

    public function getMinProd(): ?int {
        return $this->minProd;
    }

    public function getMinStock(): ?int {
        return $this->minStock;
    }

    public function getNotes(): ?string {
        return $this->notes;
    }

    public function getPackaging(): ?int {
        return $this->packaging;
    }

    public function getPackagingKind(): ?string {
        return $this->packagingKind;
    }

    public function getParent(): ?self {
        return $this->parent;
    }

    public function getPlace(): ?string {
        return $this->place;
    }

    public function getPrice(): ?float {
        return $this->price;
    }

    public function getPriceWithoutCopper(): ?float {
        return $this->priceWithoutCopper;
    }

    public function getProductionDelay(): ?int {
        return $this->productionDelay;
    }

    public function getTransfertPriceSupplies(): ?float {
        return $this->transfertPriceSupplies;
    }

    public function getTransfertPriceWork(): ?float {
        return $this->transfertPriceWork;
    }

    public function getWeight(): Measure {
        return $this->weight;
    }

    public function removeChild(self $child): self {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;

        return $this;
    }

    public function setCurrentPlaceName(string $currentPlaceName): self {
        $currentPlace = new CurrentPlace();
        $currentPlace->setName($currentPlaceName);

        $this->currentPlaceName = $currentPlaceName;
        $this->currentPlace = $currentPlace;

        return $this;
    }

    public function setCustomsCode(?string $customsCode): self {
        $this->customsCode = $customsCode;

        return $this;
    }

    public function setExpirationDate(?DateTimeInterface $expirationDate): self {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function setFamily(?Family $family): self {
        $this->family = $family;

        return $this;
    }

    public function setForecastVolume(Measure $forecastVolume): self {
        $this->forecastVolume = $forecastVolume;

        return $this;
    }

    public function setIncoterms(?Incoterms $incoterms): self {
        $this->incoterms = $incoterms;

        return $this;
    }

    public function setIndex(string $index): self {
        $this->index = $index;

        return $this;
    }

    public function setInternalIndex(int $internalIndex): self {
        $this->internalIndex = $internalIndex;

        return $this;
    }

    public function setKind(string $kind): self {
        $this->kind = $kind;

        return $this;
    }

    public function setManagedCopper(bool $managedCopper): self {
        $this->managedCopper = $managedCopper;

        return $this;
    }

    public function setMaxProto(int $maxProto): self {
        $this->maxProto = $maxProto;

        return $this;
    }

    public function setMinDelivery(int $minDelivery): self {
        $this->minDelivery = $minDelivery;

        return $this;
    }

    public function setMinProd(int $minProd): self {
        $this->minProd = $minProd;

        return $this;
    }

    public function setMinStock(int $minStock): self {
        $this->minStock = $minStock;

        return $this;
    }

    public function setNotes(?string $notes): self {
        $this->notes = $notes;

        return $this;
    }

    public function setPackaging(int $packaging): self {
        $this->packaging = $packaging;

        return $this;
    }

    public function setPackagingKind(?string $packagingKind): self {
        $this->packagingKind = $packagingKind;

        return $this;
    }

    public function setParent(?self $parent): self {
        $this->parent = $parent;

        return $this;
    }

    public function setPlace(?string $place): self {
        $this->place = $place;

        return $this;
    }

    public function setPrice(float $price): self {
        $this->price = $price;

        return $this;
    }

    public function setPriceWithoutCopper(float $priceWithoutCopper): self {
        $this->priceWithoutCopper = $priceWithoutCopper;

        return $this;
    }

    public function setProductionDelay(int $productionDelay): self {
        $this->productionDelay = $productionDelay;

        return $this;
    }

    public function setTransfertPriceSupplies(float $transfertPriceSupplies): self {
        $this->transfertPriceSupplies = $transfertPriceSupplies;

        return $this;
    }

    public function setTransfertPriceWork(float $transfertPriceWork): self {
        $this->transfertPriceWork = $transfertPriceWork;

        return $this;
    }

    public function setWeight(Measure $weight): self {
        $this->weight = $weight;

        return $this;
    }
}
