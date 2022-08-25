<?php

namespace App\Entity\Project\Product;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Doctrine\DBAL\Types\Project\Product\KindType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Project\Product\Product\State;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Logistics\Incoterms;
use App\Entity\Management\Unit;
use App\Entity\Traits\BarCodeTrait;
use App\Filter\RelationFilter;
use App\Repository\Project\Product\ProductRepository;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Validator as AppAssert;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: DateFilter::class, properties: ['endOfLife']),
    ApiFilter(filterClass: OrderFilter::class, properties: ['code', 'index', 'kind']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['family']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['code' => 'partial', 'index' => 'partial', 'kind' => 'partial']),
    ApiResource(
        description: 'Produit',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:measure', 'read:product:collection', 'read:state'],
                    'openapi_definition_name' => 'Product-collection',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les produits',
                    'summary' => 'Récupère les produits'
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
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')',
                'validation_groups' => ['Product-create']
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
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')',
                'validation_groups' => ['Product-clone']
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
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'process',
                        'required' => true,
                        'schema' => [
                            'enum' => ['admin', 'logistics', 'main', 'production', 'project'],
                            'type' => 'string'
                        ]
                    ]],
                    'summary' => 'Modifie un produit'
                ],
                'path' => '/products/{id}/{process}',
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')',
                'validation_groups' => AppAssert\ProcessGroupsGenerator::class
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite le produit à son prochain statut de workflow',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'transition',
                        'required' => true,
                        'schema' => ['enum' => State::TRANSITIONS, 'type' => 'string']
                    ]],
                    'requestBody' => null,
                    'summary' => 'Transite le produit à son prochain statut de workflow'
                ],
                'path' => '/products/{id}/promote/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_WRITER.'\')',
                'validate' => false
            ],
            'upgrade' => [
                'controller' => PlaceholderAction::class,
                'denormalization_context' => [
                    'groups' => ['write:product:clone'],
                    'openapi_definition_name' => 'Product-upgrade'
                ],
                'method' => 'POST',
                'openapi_context' => [
                    'description' => 'Évolue le produit au prochain indice',
                    'summary' => 'Évolue le produit au prochain indice'
                ],
                'path' => '/products/{id}/upgrade',
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')',
                'validation_groups' => ['Product-clone']
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
            'groups' => ['read:measure', 'read:product', 'read:state'],
            'openapi_definition_name' => 'Product-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity(repositoryClass: ProductRepository::class),
    UniqueEntity(fields: ['code', 'index'], groups: ['Product-admin', 'Product-clone', 'Product-create'])
]
class Product extends Entity implements BarCodeInterface, MeasuredInterface {
    use BarCodeTrait;

    #[
        ApiProperty(description: 'Temps auto', openapiContext: ['$ref' => '#/components/schemas/Measure-duration']),
        ORM\Embedded
    ]
    private Measure $autoDuration;

    /** @var Collection<int, self> */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $children;

    #[
        ApiProperty(description: 'Référence', example: '54587F'),
        Assert\Length(min: 3, max: 30),
        ORM\Column(length: 30),
        Serializer\Groups(['create:product', 'read:product', 'read:product:collection', 'write:product', 'write:product:admin', 'write:product:clone'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Temps auto chiffrage', openapiContext: ['$ref' => '#/components/schemas/Measure-duration']),
        ORM\Embedded
    ]
    private Measure $costingAutoDuration;

    #[
        ApiProperty(description: 'Temps manu chiffrage', openapiContext: ['$ref' => '#/components/schemas/Measure-duration']),
        ORM\Embedded
    ]
    private Measure $costingManualDuration;

    #[
        ApiProperty(description: 'Code douanier', required: false, example: '8544300089'),
        Assert\Length(min: 4, max: 10, groups: ['Product-logistics']),
        ORM\Column(length: 10, nullable: true),
        Serializer\Groups(['read:product', 'write:product:logistics'])
    ]
    private ?string $customsCode = null;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:product', 'read:product:collection'])
    ]
    private State $embState;

    #[
        ApiProperty(description: 'Date d\'expiration', example: '2021-01-12'),
        Assert\GreaterThan(value: 'today', groups: ['Product-create', 'Product-project']),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['create:product', 'read:product', 'read:product:collection', 'write:product:project'])
    ]
    private ?DateTimeImmutable $endOfLife = null;

    #[
        ApiProperty(description: 'Famille de produit', readableLink: false, example: '/api/product-families/1'),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['create:product', 'read:product', 'read:product:collection'])
    ]
    private ?Family $family = null;

    #[
        ApiProperty(description: 'Volume prévisionnel', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        AppAssert\Measure(groups: ['Product-create']),
        ORM\Embedded,
        Serializer\Groups(['create:product', 'read:product'])
    ]
    private Measure $forecastVolume;

    #[
        ApiProperty(description: 'Incoterms', readableLink: false, required: true, example: '/api/incoterms/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:product', 'write:product:logistics'])
    ]
    private ?Incoterms $incoterms = null;

    #[
        ApiProperty(description: 'Indice', required: false, example: '02'),
        Assert\Length(min: 1, max: 3, groups: ['Product-admin', 'Product-create']),
        ORM\Column(name: '`index`', length: 3),
        Serializer\Groups(['create:product', 'read:product', 'read:product:collection', 'write:product', 'write:product:admin', 'write:product:clone'])
    ]
    private ?string $index = null;

    #[
        ApiProperty(description: 'Indice interne', required: true, example: 1),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(type: 'tinyint', options: ['default' => 1, 'unsigned' => true]),
        Serializer\Groups(['read:product'])
    ]
    private int $internalIndex = 1;

    #[
        ApiProperty(description: 'Type', example: KindType::TYPE_PROTOTYPE, openapiContext: ['enum' => KindType::TYPES]),
        Assert\Choice(choices: KindType::TYPES, groups: ['Product-admin', 'Product-create', 'Product-project']),
        ORM\Column(type: 'product_kind', options: ['default' => KindType::TYPE_PROTOTYPE]),
        Serializer\Groups(['create:product', 'read:product', 'read:product:collection', 'write:product', 'write:product:admin', 'write:product:project'])
    ]
    private ?string $kind = KindType::TYPE_PROTOTYPE;

    #[
        ApiProperty(description: 'Gestion cuivre', required: false, example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:product'])
    ]
    private bool $managedCopper = false;

    #[
        ApiProperty(description: 'Temps manu', openapiContext: ['$ref' => '#/components/schemas/Measure-duration']),
        ORM\Embedded
    ]
    private Measure $manualDuration;

    #[
        ApiProperty(description: 'Nombre max de prototypes', required: true, openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        AppAssert\Measure(groups: ['Product-project']),
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product:project'])
    ]
    private Measure $maxProto;

    #[
        ApiProperty(description: 'Délai de livraison minimum', required: true, openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product:logistics'])
    ]
    private Measure $minDelivery;

    #[
        ApiProperty(description: 'Production minimum', required: true, openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        AppAssert\Measure(groups: ['Product-production']),
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product:production'])
    ]
    private Measure $minProd;

    #[
        ApiProperty(description: 'Stock minimum', required: true, openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        AppAssert\Measure,
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product:logistics'])
    ]
    private Measure $minStock;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'HEATING WIRE (HSR25304)'),
        Assert\Length(min: 3, max: 80),
        Assert\NotBlank(groups: ['Product-admin', 'Product-create']),
        ORM\Column(length: 80),
        Serializer\Groups(['create:product', 'read:product', 'read:product:collection', 'write:product', 'write:product:admin'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Produit préféré des clients'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['create:product', 'read:product', 'write:product:main'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Conditionnement', required: true, openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        AppAssert\Measure(groups: ['Product-create', 'Product-production']),
        ORM\Embedded,
        Serializer\Groups(['create:product', 'read:product', 'write:product:production'])
    ]
    private Measure $packaging;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Type de packaging'),
        Assert\Length(max: 30, groups: ['Product-create', 'Product-production']),
        ORM\Column(length: 30),
        Serializer\Groups(['create:product', 'read:product', 'write:product:production'])
    ]
    private ?string $packagingKind = null;

    #[
        ApiProperty(description: 'Unité parente', readableLink: false, example: '/api/products/3'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:product'])
    ]
    private ?self $parent = null;

    #[
        ApiProperty(description: 'Prix', required: true, openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:product'])
    ]
    private Measure $price;

    #[
        ApiProperty(description: 'Prix sans cuivre', required: true, openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:product'])
    ]
    private Measure $priceWithoutCopper;

    #[
        ApiProperty(description: 'Délai de production', required: true, openapiContext: ['$ref' => '#/components/schemas/Measure-duration']),
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product:production'])
    ]
    private Measure $productionDelay;

    #[
        ApiProperty(description: 'Prix de cession des composants', required: true, openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:product'])
    ]
    private Measure $transfertPriceSupplies;

    #[
        ApiProperty(description: 'Prix de cession de main d\'œuvre', required: true, openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:product'])
    ]
    private Measure $transfertPriceWork;

    #[
        ApiProperty(description: 'Unité', readableLink: false, required: true, example: '/api/units/1'),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['create:product', 'read:product'])
    ]
    private ?Unit $unit = null;

    #[
        ApiProperty(description: 'Poids', openapiContext: ['$ref' => '#/components/schemas/Measure-mass']),
        ORM\Embedded,
        Serializer\Groups(['read:product', 'write:product:logistics'])
    ]
    private Measure $weight;

    public function __construct() {
        $this->autoDuration = new Measure();
        $this->children = new ArrayCollection();
        $this->costingAutoDuration = new Measure();
        $this->costingManualDuration = new Measure();
        $this->embState = new State();
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

    public function __clone() {
        parent::__clone();
        $this->children = new ArrayCollection();
        $this->embState = new State();
        $this->internalIndex = 1;
        $this->parent = null;
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

    final public function getCode(): ?string {
        return $this->code;
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

    final public function getEmbState(): State {
        return $this->embState;
    }

    final public function getEndOfLife(): ?DateTimeImmutable {
        return $this->endOfLife;
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

    final public function getMeasures(): array {
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

    final public function getMinDelivery(): Measure {
        return $this->minDelivery;
    }

    final public function getMinProd(): Measure {
        return $this->minProd;
    }

    final public function getMinStock(): Measure {
        return $this->minStock;
    }

    final public function getName(): ?string {
        return $this->name;
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

    /**
     * @return array<string, 1>
     */
    final public function getState(): array {
        return $this->embState->getState();
    }

    final public function getTransfertPriceSupplies(): Measure {
        return $this->transfertPriceSupplies;
    }

    final public function getTransfertPriceWork(): Measure {
        return $this->transfertPriceWork;
    }

    final public function getUnit(): ?Unit {
        return $this->unit;
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

    final public function setCode(?string $code): self {
        $this->code = $code;
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

    final public function setEmbState(State $embState): self {
        $this->embState = $embState;
        return $this;
    }

    final public function setEndOfLife(?DateTimeImmutable $endOfLife): self {
        $this->endOfLife = $endOfLife;
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

    final public function setName(?string $name): self {
        $this->name = $name;
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

    /**
     * @param array<string, 1> $state
     */
    final public function setState(array $state): self {
        $this->embState->setState($state);
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

    final public function setUnit(?Unit $unit): self {
        $this->unit = $unit;
        return $this;
    }

    final public function setWeight(Measure $weight): self {
        $this->weight = $weight;
        return $this;
    }
}
