<?php

namespace App\Entity\Purchase\Component;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Blocker;
use App\Entity\Embeddable\ComponentManufacturingOperationState;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Entity\Traits\BarCodeTrait;
use App\Filter\RelationFilter;
use App\Repository\Purchase\Component\ComponentRepository;
use App\Validator as AppAssert;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['family', 'index', 'name']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['family']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['index' => 'partial', 'name' => 'partial']),
    ApiResource(
        description: 'Composant',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:component:collection', 'read:measure', 'read:state'],
                    'openapi_definition_name' => 'Component-collection',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les composants',
                    'summary' => 'Récupère les composants'
                ]
            ],
            'post' => [
                'denormalization_context' => [
                    'groups' => ['create:component', 'write:measure'],
                    'openapi_definition_name' => 'Component-create'
                ],
                'openapi_context' => [
                    'description' => 'Créer un composant',
                    'summary' => 'Créer un composant'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')',
                'validation_groups' => ['Component-create']
            ]
        ],
        itemOperations: [
            'clone' => [
                'controller' => PlaceholderAction::class,
                'denormalization_context' => [
                    'groups' => ['write:component:clone'],
                    'openapi_definition_name' => 'Component-clone'
                ],
                'method' => 'POST',
                'openapi_context' => [
                    'description' => 'Clone un composant',
                    'summary' => 'Clone un composant',
                ],
                'path' => '/components/{id}/clone',
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')',
                'validation_groups' => ['Component-clone']
            ],
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un composant',
                    'summary' => 'Supprime un composant'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un composant',
                    'summary' => 'Récupère un composant',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un composant',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'process',
                        'required' => true,
                        'schema' => [
                            'enum' => ['admin', 'logistics', 'main', 'price', 'purchase', 'quality'],
                            'type' => 'string'
                        ]
                    ]],
                    'summary' => 'Modifie un composant'
                ],
                'path' => '/components/{id}/{process}',
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')',
                'validation_groups' => AppAssert\ProcessGroupsGenerator::class
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite le composant à son prochain statut de workflow',
                    'parameters' => [
                        [
                            'in' => 'path',
                            'name' => 'transition',
                            'required' => true,
                            'schema' => ['enum' => [...ComponentManufacturingOperationState::TRANSITIONS, ...Blocker::TRANSITIONS], 'type' => 'string']
                        ],
                        [
                            'in' => 'path',
                            'name' => 'workflow',
                            'required' => true,
                            'schema' => ['enum' => ['component', 'blocker'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite le composant à son prochain statut de workflow'
                ],
                'path' => '/components/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')',
                'validate' => false
            ],
            'upgrade' => [
                'controller' => PlaceholderAction::class,
                'denormalization_context' => [
                    'groups' => ['write:component:clone'],
                    'openapi_definition_name' => 'Component-upgrade'
                ],
                'method' => 'POST',
                'openapi_context' => [
                    'description' => 'Évolue le composant au prochain indice',
                    'summary' => 'Évolue le composant au prochain indice'
                ],
                'path' => '/components/{id}/upgrade',
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')',
                'validation_groups' => ['Component-clone']
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:component', 'write:measure'],
            'openapi_definition_name' => 'Component-write'
        ],
        normalizationContext: [
            'groups' => ['read:component', 'read:measure', 'read:state'],
            'openapi_definition_name' => 'Component-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity(repositoryClass: ComponentRepository::class)
]
class Component extends Entity implements BarCodeInterface, MeasuredInterface {
    use BarCodeTrait;

    /** @var Collection<int, ComponentAttribute> */
    #[ORM\OneToMany(mappedBy: 'component', targetEntity: ComponentAttribute::class, cascade: ['remove'])]
    private Collection $attributes;

    #[
        ApiProperty(description: 'Poids cuivre', openapiContext: ['$ref' => '#/components/schemas/Measure-linear-density']),
        ORM\Embedded,
        Serializer\Groups(['read:component', 'write:component', 'write:component:price'])
    ]
    private Measure $copperWeight;

    #[
        ApiProperty(description: 'Code douanier', required: false, example: '8544300089'),
        Assert\Length(min: 4, max: 16, groups: ['Component-logistics']),
        ORM\Column(length: 16, nullable: true),
        Serializer\Groups(['read:component', 'write:component:logistics'])
    ]
    private ?string $customsCode = null;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:component', 'read:component:collection'])
    ]
    private Blocker $embBlocker;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:component', 'read:component:collection'])
    ]
    private ComponentManufacturingOperationState $embState;

    #[
        ApiProperty(description: 'Date de fin de vie', required: false, example: '2021-11-18'),
        Assert\GreaterThan(value: 'today', groups: ['Component-create', 'Component-logistics']),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['create:component', 'read:component', 'read:component:collection', 'write:component:logistics'])
    ]
    private ?DateTimeImmutable $endOfLife = null;

    #[
        ApiProperty(description: 'Famille', readableLink: false, required: true, example: '/api/component-families/1'),
        Assert\NotBlank(groups: ['Component-admin', 'Component-create']),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne(targetEntity: Family::class, inversedBy: 'components'),
        Serializer\Groups(['create:component', 'read:component', 'read:component:collection', 'write:component', 'write:component:admin'])
    ]
    private ?Family $family = null;

    #[
        ApiProperty(description: 'Poids cuivre', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        AppAssert\Measure(groups: ['Component-logistics']),
        ORM\Embedded,
        Serializer\Groups(['read:component', 'write:component', 'write:component:logistics'])
    ]
    private Measure $forecastVolume;

    #[
        ApiProperty(description: 'Indice', required: true, example: '1'),
        Assert\Length(max: 5, groups: ['Component-admin', 'Component-clone']),
        Assert\NotBlank(groups: ['Component-admin', 'Component-clone']),
        ORM\Column(name: '`index`', length: 5, nullable: false, options: ['default' => '0']),
        Serializer\Groups(['read:component', 'read:component:collection', 'write:component', 'write:component:admin', 'write:component:clone'])
    ]
    private string $index = '0';

    #[
        ApiProperty(description: 'Gestion en stock', required: true, example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['create:component', 'read:component', 'write:component', 'write:component:logistics'])
    ]
    private bool $managedStock = false;

    #[
        ApiProperty(description: 'Fabricant', required: false, example: 'scapa'),
        Assert\NotBlank(groups: ['Component-create', 'Component-purchase']),
        ORM\Column(nullable: true),
        Serializer\Groups(['create:component', 'read:component', 'write:component', 'write:component:purchase'])
    ]
    private ?string $manufacturer = null;

    #[
        ApiProperty(description: 'Référence fabricant', required: false, example: '103078'),
        Assert\NotBlank(groups: ['Component-create', 'Component-purchase']),
        ORM\Column(nullable: true),
        Serializer\Groups(['create:component', 'read:component', 'write:component', 'write:component:purchase'])
    ]
    private ?string $manufacturerCode = null;

    #[
        ApiProperty(description: 'Stock minimum', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        AppAssert\Measure(groups: ['Component-logistics']),
        ORM\Embedded,
        Serializer\Groups(['read:component', 'write:component', 'write:component:logistics'])
    ]
    private Measure $minStock;

    #[
        ApiProperty(description: 'Nom', required: true, example: '2702 SCOTCH ADHESIF PVC T2 19MMX33M NOIR'),
        Assert\NotBlank(groups: ['Component-admin', 'Component-create']),
        ORM\Column,
        Serializer\Groups(['create:component', 'read:component', 'read:component:collection', 'write:component', 'write:component:admin', 'write:component:clone'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Besoin de join', required: true, example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:component', 'write:component', 'write:component:main'])
    ]
    private bool $needGasket = false;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Lorem Ipsum'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:component', 'write:component', 'write:component:main'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Info commande', required: false, example: 'Ipsum Lorem id est'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:component', 'write:component', 'write:component:price'])
    ]
    private ?string $orderInfo = null;

    #[
        ApiProperty(description: 'Parent', readableLink: false, required: false, example: '/api/components/1'),
        ORM\ManyToOne(targetEntity: self::class),
        Serializer\Groups(['read:component'])
    ]
    private ?self $parent = null;

    #[
        ApiProperty(description: 'Taux ppm', required: false, example: '10'),
        Assert\NotNull(groups: ['Component-quality']),
        Assert\PositiveOrZero(groups: ['Component-quality']),
        ORM\Column(type: 'smallint', options: ['default' => 10, 'unsigned' => true]),
        Serializer\Groups(['read:component', 'write:component', 'write:component:quality'])
    ]
    private int $ppmRate = 10;

    #[
        ApiProperty(description: 'Unité', readableLink: false, required: false, example: '/api/units/1'),
        Assert\NotBlank(groups: ['Component-create', 'Component-logistics']),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne(fetch: 'EAGER'),
        Serializer\Groups(['create:component', 'read:component', 'write:component', 'write:component:logistics'])
    ]
    private ?Unit $unit = null;

    #[
        ApiProperty(description: 'Poids', openapiContext: ['$ref' => '#/components/schemas/Measure-mass']),
        ORM\Embedded,
        Serializer\Groups(['read:component', 'write:component', 'write:component:logistics'])
    ]
    private Measure $weight;

    public function __construct() {
        $this->attributes = new ArrayCollection();
        $this->copperWeight = new Measure();
        $this->embBlocker = new Blocker();
        $this->embState = new ComponentManufacturingOperationState();
        $this->forecastVolume = new Measure();
        $this->minStock = new Measure();
        $this->weight = new Measure();
    }

    public function __clone() {
        parent::__clone();
        $this->attributes = new ArrayCollection();
        $this->embBlocker = new Blocker();
        $this->embState = new ComponentManufacturingOperationState();
    }

    public static function getBarCodeTableNumber(): string {
        return self::COMPONENT_BAR_CODE_TABLE_NUMBER;
    }

    final public function addAttribute(ComponentAttribute $attribute): self {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes->add($attribute);
            $attribute->setComponent($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, ComponentAttribute>
     */
    final public function getAttributes(): Collection {
        return $this->attributes;
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
    }

    #[
        ApiProperty(description: 'Référence interne', required: true, example: 'FIX-1'),
        Serializer\Groups(['read:component', 'read:component:collection'])
    ]
    final public function getCode(): ?string {
        return "{$this->family?->getCode()}-{$this->getId()}";
    }

    final public function getCopperWeight(): Measure {
        return $this->copperWeight;
    }

    final public function getCustomsCode(): ?string {
        return $this->customsCode;
    }

    final public function getEmbBlocker(): Blocker {
        return $this->embBlocker;
    }

    final public function getEmbState(): ComponentManufacturingOperationState {
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

    final public function getIndex(): string {
        return $this->index;
    }

    final public function getManufacturer(): ?string {
        return $this->manufacturer;
    }

    final public function getManufacturerCode(): ?string {
        return $this->manufacturerCode;
    }

    public function getMeasures(): array {
        return [$this->copperWeight, $this->forecastVolume, $this->minStock, $this->weight];
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

    final public function getOrderInfo(): ?string {
        return $this->orderInfo;
    }

    final public function getParent(): ?self {
        return $this->parent;
    }

    final public function getPpmRate(): int {
        return $this->ppmRate;
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    final public function getUnit(): ?Unit {
        return $this->unit;
    }

    final public function getWeight(): Measure {
        return $this->weight;
    }

    final public function isManagedStock(): bool {
        return $this->managedStock;
    }

    final public function isNeedGasket(): bool {
        return $this->needGasket;
    }

    final public function removeAttribute(ComponentAttribute $attribute): self {
        if ($this->attributes->contains($attribute)) {
            $this->attributes->removeElement($attribute);
            if ($attribute->getComponent() === $this) {
                $attribute->setComponent(null);
            }
        }
        return $this;
    }

    final public function setBlocker(string $state): self {
        $this->embBlocker->setState($state);
        return $this;
    }

    final public function setCopperWeight(Measure $copperWeight): self {
        $this->copperWeight = $copperWeight;
        return $this;
    }

    final public function setCustomsCode(?string $customsCode): self {
        $this->customsCode = $customsCode;
        return $this;
    }

    final public function setEmbBlocker(Blocker $embBlocker): self {
        $this->embBlocker = $embBlocker;
        return $this;
    }

    final public function setEmbState(ComponentManufacturingOperationState $embState): self {
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

    final public function setIndex(string $index): self {
        $this->index = $index;
        return $this;
    }

    final public function setManagedStock(bool $managedStock): self {
        $this->managedStock = $managedStock;
        return $this;
    }

    final public function setManufacturer(?string $manufacturer): self {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    final public function setManufacturerCode(?string $manufacturerCode): self {
        $this->manufacturerCode = $manufacturerCode;
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

    final public function setNeedGasket(bool $needGasket): self {
        $this->needGasket = $needGasket;
        return $this;
    }

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }

    final public function setOrderInfo(?string $orderInfo): self {
        $this->orderInfo = $orderInfo;
        return $this;
    }

    final public function setParent(?self $parent): self {
        $this->parent = $parent;
        return $this;
    }

    final public function setPpmRate(int $ppmRate): self {
        $this->ppmRate = $ppmRate;
        return $this;
    }

    final public function setState(string $state): self {
        $this->embState->setState($state);
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
