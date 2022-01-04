<?php

namespace App\Entity\Purchase\Component;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Purchase\Component\CurrentPlace;
use App\Entity\Entity;
use App\Entity\Management\Unit;
use App\Entity\Quality\Reception\ComponentReference;
use App\Entity\Traits\CodeTrait;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\RefTrait;
use App\Filter\RelationFilter;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'ref' => 'partial',
        'index' => 'partial',
        'name' => 'partial'
    ]),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'family' => 'name',
        'currentPlace' => 'name'
    ]),
    ApiFilter(filterClass: OrderFilter::class, properties: [
        'ref',
        'index',
        'name'
    ]),
    ApiResource(
        description: 'Composant',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:name', 'read:component:collection']
                ],
                'openapi_context' => [
                    'description' => 'Récupère les composants',
                    'summary' => 'Récupère les composants'
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un composant',
                    'summary' => 'Créer un composant'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un composant',
                    'summary' => 'Supprime un composant'
                ]
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un composant',
                    'summary' => 'Récupère un composant',
                ]
            ],
            'patch' => [
                'path' => '/components/{id}/{process}',
                'requirements' => ['process' => '\w+'],
                'openapi_context' => [
                    'description' => 'Modifier un composant',
                    'summary' => 'Modifier un composant',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'process',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                            'enum' => ['admin', 'quality', 'purchase', 'logistics', 'price']
                        ]
                    ]]
                ]
            ],
            'clone' => [
                'method' => 'POST',
                'path' => '/components/{id}/clone',
                'controller' => PlaceholderAction::class,
                'openapi_context' => [
                    'description' => 'Clone un composant',
                    'summary' => 'Clone un composant',
                ],
                'denormalization_context' => [
                    'groups' => ['write:component:clone']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ],
            'upgrade' => [
                'method' => 'POST',
                'path' => '/components/{id}/upgrade',
                'controller' => PlaceholderAction::class,
                'openapi_context' => [
                    'description' => 'Dupliquer un composant en le reliant à son parent',
                    'summary' => 'Dupliquer un composant en le reliant à son parent',
                ],
                'denormalization_context' => [
                    'groups' => ['write:component:upgrade']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ],
            'promote' => [
                'method' => 'PATCH',
                'path' => '/components/{id}/promote',
                'controller' => PlaceholderAction::class,
                'openapi_context' => [
                    'description' => 'Passer un composant à un nouveau statut',
                    'summary' => 'Passer un composant à un nouveau statut',
                ],
                'denormalization_context' => [
                    'groups' => ['write:component:promote']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:code', 'write:create', 'write:name', 'write:measure', 'writer:current_place', 'write:component', 'write:ref', 'write:equivalents', 'write:unit'],
            'openapi_definition_name' => 'Component-write'
        ],
        normalizationContext: [
            'groups' => ['read:code', 'read:component', 'read:id', 'read:name', 'read:measure', 'read:current_place', 'read:ref', 'read:equivalents', 'read:unit'],
            'openapi_definition_name' => 'Component-read'
        ]
    ),
    ORM\Entity
]
class Component extends Entity {
    use CodeTrait;
    use NameTrait;
    use NameTrait, RefTrait {
        RefTrait::__toString insteadof NameTrait;
    }

    #[
        ApiProperty(description: 'Référence interne', required: true, example: 'FIX-1'),
        Assert\Length(max: 10, groups: ['write:create']),
        Assert\NotBlank(groups: ['write:create']),
        ORM\Column(length: 10, nullable: true),
        Serializer\Groups(['read:code'])
    ]
    protected ?string $code = null;

    #[
        ApiProperty(description: 'Statut'),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:current_place', 'read:component:collection'])
    ]
    protected CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Nom', required: true, example: '2702 SCOTCH ADHESIF PVC T2 19MMX33M NOIR'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name', 'read:component:collection'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Référence', example: '54587F'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:ref', 'read:component', 'read:component:collection',  'write:component:clone', 'write:component:upgrade'])
    ]
    protected ?string $ref = null;

    /**
     * @var Collection<int, self>
     */
    #[
        ApiProperty(description: 'Composant enfant', required: false, readableLink: false, example: ['/api/components/5', '/api/components/14']),
        ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['persist', 'remove']),
        Serializer\Groups(['read:component'])
    ]
    private Collection $children;

    #[
        ApiProperty(description: 'Poids du cuivre', example: 0),
        ORM\Embedded(Measure::class),
        Serializer\Groups(['read:measure'])
    ]
    private Measure $copperWeight;

    #[
        ApiProperty(description: 'Code frontière', required: false, example: '2022RK'),
        Assert\Length(max: 255),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:component'])
    ]
    private ?string $customsCode = null;

    #[
        ApiProperty(description: 'Date de fin de vie', required: true, example: '2021-11-18'),
        Assert\Date(groups: ['write:create']),
        Assert\NotBlank(groups: ['write:create']),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:component'])
    ]
    private ?DateTimeImmutable $endOfLife = null;

    #[
        ApiProperty(description: 'Equivalents', readableLink: false, example: ['/api/equivalents/1', '/api/equivalents/2']),
        ORM\ManyToOne(targetEntity: Equivalents::class, inversedBy: 'components'),
        Serializer\Groups(['read:equivalents', 'write:requivalents', 'read:component'])
    ]
    private ?Equivalents $equivalents = null;

    #[
        ApiProperty(description: 'Famille', readableLink: false, required: true, example: '/api/component-families/1'),
        Assert\NotBlank(groups: ['write:create']),
        ORM\ManyToOne(targetEntity: Family::class),
        Serializer\Groups(['read:component', 'write:component', 'read:component:collection'])
    ]
    private ?Family $family = null;

    #[
        ApiProperty(description: 'Volume prévisionnel', example: '2000'),
        ORM\Embedded(Measure::class),
        Serializer\Groups(['read:measure'])
    ]
    private Measure $forecastVolume;

    #[
        ApiProperty(description: 'Indice', required: true, example: '1'),
        Assert\Length(max: 5),
        ORM\Column(length: 5, name: 'component_index'),
        Serializer\Groups(['read:component', 'read:component:collection', 'write:component:clone', 'write:component:upgrade'])
    ]
    private string $index = '0';

    #[
        ApiProperty(description: 'Gestion en stock', example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:component', 'write:create'])
    ]
    private bool $managedStock = false;

    #[
        ApiProperty(description: 'Fabricant', required: true, example: 'scapa'),
        Assert\NotBlank(groups: ['write:create']),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:component', 'write:create'])
    ]
    private ?string $manufacturer = null;

    #[
        ApiProperty(description: 'Référence fabricant', required: true, example: '103078'),
        Assert\NotBlank(groups: ['write:create']),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:component'])
    ]
    private ?string $manufacturerRef = null;

    #[
        ApiProperty(description: 'Stock minimum', example: 221_492),
        ORM\Embedded(Measure::class),
        Serializer\Groups(['read:measure'])
    ]
    private Measure $minStock;

    #[
        ApiProperty(description: 'Besoin de join', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:component'])
    ]
    private bool $needGasket = false;

    #[
        ApiProperty(description: 'Notes'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:component'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Info commande'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:component'])
    ]
    private ?string $orderInfo = null;

    #[
        ApiProperty(description: 'Equivalent', readableLink: false, example: '/api/components/1'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:component'])
    ]
    private ?self $parent = null;

    #[
        ApiProperty(description: 'Nouveau statut', required: false, example: 'draft'),
        Serializer\Groups(['write:component:promote'])
    ]
    private ?string $place = null;

    #[
        ApiProperty(description: 'Taux ppm', required: false, example: '10'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'smallint'),
        Serializer\Groups(['read:component'])
    ]
    private ?int $ppmRate = 0;

    #[
        ApiProperty(description: 'Qualité', example: 2),
        ORM\Column(options: ['unsigned' => true], nullable: true),
        Serializer\Groups(['read:component'])
    ]
    private ?int $quality = null;

    /** @var Collection<int, ComponentReference> */
    #[
        ApiProperty(description: 'References'),
        ORM\ManyToMany(targetEntity: ComponentReference::class, mappedBy: 'items'),
        Serializer\Groups(['read:component'])
    ]
    private Collection $references;

    #[
        ApiProperty(description: 'Unité', readableLink: false, example: '/api/units/1'),
        ORM\ManyToOne(targetEntity: Unit::class),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    private ?Unit $unit = null;

    #[
        ApiProperty(description: 'Poids', example: '3.0378'),
        ORM\Embedded(Measure::class),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private Measure $weight;

    public function __construct() {
        $this->weight = new Measure();
        $this->forecastVolume = new Measure();
        $this->minStock = new Measure();
        $this->copperWeight = new Measure();
        $this->children = new ArrayCollection();
        $this->currentPlace = new CurrentPlace();
        $this->references = new ArrayCollection();
    }

    final public function addChild(self $child): self {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    final public function addReference(ComponentReference $references): self {
        if (!$this->references->contains($references)) {
            $this->references[] = $references;
            $references->addItem($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    final public function getChildren(): Collection {
        return $this->children;
    }

    final public function getCopperWeight(): Measure {
        return $this->copperWeight;
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getCustomsCode(): ?string {
        return $this->customsCode;
    }

    final public function getEndOfLife(): ?DateTimeImmutable {
        return $this->endOfLife;
    }

    final public function getEquivalents(): ?Equivalents {
        return $this->equivalents;
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

    final public function getManufacturerRef(): ?string {
        return $this->manufacturerRef;
    }

    final public function getMinStock(): Measure {
        return $this->minStock;
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

    final public function getPlace(): ?string {
        return $this->place;
    }

    final public function getPpmRate(): ?int {
        return $this->ppmRate;
    }

    final public function getQuality(): ?int {
        return $this->quality;
    }

    /**
     * @return Collection<int, ComponentReference>
     */
    final public function getReferences(): Collection {
        return $this->references;
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

    final public function removeReference(ComponentReference $references): self {
        if ($this->references->removeElement($references)) {
            $references->removeItem($this);
        }

        return $this;
    }

    final public function setCopperWeight(Measure $copperWeight): self {
        $this->copperWeight = $copperWeight;
        return $this;
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;

        return $this;
    }

    final public function setCustomsCode(?string $customsCode): self {
        $this->customsCode = $customsCode;

        return $this;
    }

    final public function setEndOfLife(?DateTimeImmutable $endOfLife): self {
        $this->endOfLife = $endOfLife;
        return $this;
    }

    final public function setEquivalents(?Equivalents $equivalents): self {
        $this->equivalents = $equivalents;
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

    final public function setManufacturerRef(?string $manufacturerRef): self {
        $this->manufacturerRef = $manufacturerRef;
        return $this;
    }

    final public function setMinStock(Measure $minStock): self {
        $this->minStock = $minStock;
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

    final public function setPlace(?string $place): self {
        $this->place = $place;

        return $this;
    }

    final public function setPpmRate(?int $ppmRate): self {
        $this->ppmRate = $ppmRate;
        return $this;
    }

    final public function setQuality(int $quality): self {
        $this->quality = $quality;
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
