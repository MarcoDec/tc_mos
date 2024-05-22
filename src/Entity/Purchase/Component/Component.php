<?php

namespace App\Entity\Purchase\Component;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Collection;
use App\Controller\Purchase\Component\ComponentController;
use App\Entity\Embeddable\Blocker;
use App\Entity\Embeddable\Purchase\Component\State;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Interfaces\FileEntity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Logistics\Component\Preparation;
use App\Entity\Management\Unit;
use App\Entity\Purchase\Component\Attachment\ComponentAttachment;
use App\Entity\Purchase\Order\ComponentItem;
use App\Entity\Quality\Reception\Check;
use App\Entity\Quality\Reception\Reference\Purchase\ComponentReference;
use App\Entity\Traits\BarCodeTrait;
use App\Entity\Traits\FileTrait;
use App\Filter\RelationFilter;
use App\Repository\Purchase\Component\ComponentRepository;
use App\Validator as AppAssert;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use PHPUnit\TextUI\XmlConfiguration\File;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Purchase\Supplier\Component as SupplierComponent;
use App\Filter\SetFilter;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['family', 'index', 'name', 'code', 'id']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['family']),
    ApiFilter(filterClass: SetFilter::class, properties: ['embState.state','embBlocker.state']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['index' => 'partial', 'name' => 'partial', 'code' => 'partial', 'id' => 'exact']),
    ApiResource(
        description: 'Composant',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:component:collection', 'read:measure', 'read:state', 'read:id'],
                    'openapi_definition_name' => 'Component-collection',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les composants',
                    'summary' => 'Récupère les composants'
                ]
            ],
            'options' => [
                'controller' => PlaceholderAction::class,
                'filters' => [],
                'method' => 'GET',
                'normalization_context' => [
                    'groups' => ['read:id', 'read:component:option'],
                    'openapi_definition_name' => 'PurchaseComponent-options',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les composants pour les select',
                    'summary' => 'Récupère les composants pour les select',
                ],
                'order' => ['name' => 'asc'],
                'pagination_enabled' => false,
                'path' => '/components/options'
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
            'patch image' => [
                'openapi_context' => [
                  'description' => 'Modifie l\'image d\'un composant',
                  'summary' => 'Modifie l\'image d\'un composant'
                ],
                'denormalization_context' => [
                    'groups' => ['write:component:image'],
                    'openapi_definition_name' => 'Component-image'
                ],
                'normalization_context' => [
                    'groups' => ['read:component:image'],
                    'openapi_definition_name' => 'Component-image'
                ],
                'path' => '/components/{id}/image',
                'controller' => PlaceholderAction::class,
                'method' => 'POST',
                'input_formats' => ['multipart'],
            ],
            'patch' => [
//                'controller' => ComponentController::class,
                'controller' => PlaceholderAction::class,
                'method' => 'PATCH',
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
                'read' => true,
                'write' => true,
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
                            'schema' => ['enum' => [...State::TRANSITIONS, ...Blocker::TRANSITIONS], 'type' => 'string']
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
            'groups' => ['read:component', 'read:measure', 'read:state', 'read:id', 'read:file'],
            'openapi_definition_name' => 'Component-read',
            'skip_null_values' => false
        ],
        paginationClientEnabled: true
    ),
    ORM\Entity(repositoryClass: ComponentRepository::class)
]
/**
 * Composant
 * @template-extends Entity<Component>
 */
class Component extends Entity implements BarCodeInterface, MeasuredInterface, FileEntity {
    use BarCodeTrait, FileTrait;

   /** @var DoctrineCollection<int, ComponentAttachment> */
    #[ ORM\OneToMany(mappedBy: 'component', targetEntity: ComponentAttachment::class) ]
    private DoctrineCollection $attachments;

    /** @var DoctrineCollection<int, ComponentAttribute> */
    #[ORM\OneToMany(mappedBy: 'component', targetEntity: ComponentAttribute::class, cascade: ['remove'])]
    private DoctrineCollection $attributes;

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
        Serializer\Groups(['read:component', 'write:component', 'write:component:logistics', 'write:component:admin'])
    ]
    private ?string $customsCode = null;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:item', 'read:component', 'read:component:collection'])
    ]
    private Blocker $embBlocker;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:item', 'read:component', 'read:component:collection'])
    ]
    private State $embState;

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
        ORM\ManyToOne(targetEntity: Family::class, fetch: 'LAZY', inversedBy: 'components'),
        Serializer\Groups(['create:component', 'read:component', 'read:component:collection', 'write:component', 'write:component:admin'])
    ]
    private ?Family $family = null;

    #[
        ApiProperty(description: 'Lien image'),
        ORM\Column(type: 'string'),
        Serializer\Groups(['read:file', 'read:component:collection', 'read:product-family'])
    ]
    protected ?string $filePath = '';

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
        Serializer\Groups(['read:item', 'read:component', 'read:component:collection', 'write:component', 'write:component:admin', 'write:component:clone'])
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
        Serializer\Groups(['read:item', 'create:component', 'read:component', 'read:component:collection', 'write:component', 'write:component:admin', 'write:component:clone', 'read:stock', 'read:component-preparation'])
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
        ApiProperty(description: 'Notation Qualité', required: true, example: '0'),
        Assert\NotNull(groups: ['Component-quality']),
        Assert\PositiveOrZero(groups: ['Component-quality']),
        ORM\Column(type: 'smallint', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:component', 'write:component', 'write:component:quality'])
    ]
    private int $quality = 0;

    #[
        ApiProperty(description: 'Gestion reach', required: true, example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:component', 'write:component', 'write:component:quality'])
    ]
    private bool $reach = false;

    /** @var DoctrineCollection<int, ComponentReference> */
    #[ORM\ManyToMany(targetEntity: ComponentReference::class, mappedBy: 'items')]
    private DoctrineCollection $references;

    #[
        ApiProperty(description: 'Gestion rohs', required: true, example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:component', 'write:component', 'write:component:quality'])
    ]
    private bool $rohs = false;

    #[
        ORM\OneToMany(targetEntity: SupplierComponent::class, mappedBy: 'component')
    ]
    private DoctrineCollection $supplierComponents;

    #[
        ApiProperty(description: 'Unité', readableLink: false, required: false, example: '/api/units/1'),
        Assert\NotBlank(groups: ['Component-create', 'Component-logistics']),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne(fetch:'LAZY'),
        Serializer\Groups(['read:component:collection', 'create:component', 'read:component', 'write:component', 'write:component:logistics'])
    ]
    private ?Unit $unit = null;

    #[
        ApiProperty(description: 'Poids', openapiContext: ['$ref' => '#/components/schemas/Measure-mass']),
        ORM\Embedded,
        Serializer\Groups(['read:component', 'write:component', 'write:component:logistics'])
    ]
    private Measure $weight;

    #[
        ApiProperty(description: 'Référence interne', required: true, example: 'FIX-1'),
        ORM\Column,
        Serializer\Groups(['read:item', 'read:component', 'read:component:collection', 'read:stock', 'read:item', 'read:component-preparation'])
    ]
    private ?string $code='';

    #[
        ApiProperty(description: 'Préparations', required: false, fetchEager: true),
        ORM\OneToMany(mappedBy: 'component', targetEntity: Preparation::class/*, fetch: 'EAGER'*/),
        Serializer\MaxDepth(1)
    ]
    private DoctrineCollection $preparationComponents;

    #[
        ApiProperty(description: 'Items de commande fournisseur associés', example: '[/api/purchase-order-items/1]', fetchEager: false),
        ORM\OneToMany(mappedBy: "item", targetEntity: ComponentItem::class),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    private DoctrineCollection $componentItems;

    public function __construct() {
        $this->attributes = new ArrayCollection();
        $this->copperWeight = new Measure();
        $this->embBlocker = new Blocker();
        $this->embState = new State();
        $this->forecastVolume = new Measure();
        $this->minStock = new Measure();
        $this->references = new ArrayCollection();
        $this->supplierComponents = new ArrayCollection();
        $this->weight = new Measure();
        $this->code = '';
        $this->code = $this->getCode();
        $this->preparationComponents = new ArrayCollection();

    }

    public function __clone() {
        parent::__clone();
        $this->attributes = new ArrayCollection();
        $this->embBlocker = new Blocker();
        $this->embState = new State();
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

    final public function addReference(ComponentReference $reference): self {
        if (!$this->references->contains($reference)) {
            $this->references->add($reference);
            $reference->addItem($this);
        }
        return $this;
    }

    /**
     * @return DoctrineCollection<int, ComponentAttribute>
     */
    final public function getAttributes(): DoctrineCollection {
        return $this->attributes;
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
    }

    /**
     * @return Collection<int, Check<self, Family|self>>
     */
    final public function getChecks(): Collection {
        $checks = Collection::collect($this->references->getValues())
            ->map(static function (ComponentReference $reference): Check {
                /** @var Check<self, self> $check */
                $check = new Check();
                return $check->setReference($reference);
            });
        if (!empty($this->family)) {
            $checks = $checks->merge($this->family->getChecks());
        }
        /** @var Collection<int, Check<self, Family|self>> $checks */
        return $checks;
    }


    final public function getCode(): ?string {
        return $this->code;
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

    public function getUnitMeasures(): array {
        return [$this->copperWeight, $this->forecastVolume, $this->minStock, $this->weight];
    }
    public function getCurrencyMeasures(): array
    {
        return [];
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

    #[
        ApiProperty(description: 'Meilleur Prix composant'),
        Serializer\Groups(['read:component'])
    ]
    public function getPrice(): Measure {
        $price = (new Measure())->setValue(0)->setCode('EUR');
        $supplierComponents = $this->getSupplierComponents()->toArray();
        if (count($supplierComponents)>0) {
            usort($supplierComponents, function ($a, $b) {
                return $a->getBestPrice()->getValue() > $b->getBestPrice()->getValue();
            });
            $price = $supplierComponents[0]->getBestPrice();
        }
        return $price;
    }

    final public function getPpmRate(): int {
        return $this->ppmRate;
    }
   
    public function getQuality()
    {
        return $this->quality;
    }

    public function getReach()
    {
        return $this->reach;
    }

    /**
     * @return DoctrineCollection<int, ComponentReference>
     */
    final public function getReferences(): DoctrineCollection {
        return $this->references;
    }

    public function getRohs()
    {
        return $this->rohs;
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    #[Serializer\Groups(['read:component:option'])]
    final public function getText(): ?string {
        return $this->getCode();
    }

    final public function setCode($code) {
        $this->code = $code;
    }

    public function getSupplierComponents()
    {
        return $this->supplierComponents;
    }

    public function getPreparationComponents()
    {
        return $this->preparationComponents;
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

    final public function removeReference(ComponentReference $reference): self {
        if ($this->references->contains($reference)) {
            $this->references->removeElement($reference);
            $reference->removeItem($this);
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
        if ($this->family) $this->code = $this->family->getCode()."-{$this->getId()}";
        else $this->code = "XXX-{$this->getId()}";
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

    public function setQuality($quality): self
    {
        $this->quality = $quality;

        return $this;
    }

    public function setReach($reach):self
    {
        $this->reach = $reach;

        return $this;
    }

    public function setRohs($rohs):self
    {
        $this->rohs = $rohs;

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

   /**
    * @return DoctrineCollection
    */
   public function getAttachments(): DoctrineCollection
   {
      return $this->attachments;
   }

   /**
    * @param DoctrineCollection $attachments
    */
   public function setAttachments(DoctrineCollection $attachments): void
   {
      $this->attachments = $attachments;
   }

    #[
        ApiProperty(description: 'Icône', example: '/uploads/component-families/1.jpg'),
        Serializer\Groups(['read:file'])
    ]
    final public function getFilepath(): ?string {
        return $this->filePath;
    }
    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }

    public function getComponentItems(): DoctrineCollection
    {
        return $this->componentItems;
    }

    public function setComponentItems(DoctrineCollection $componentItems): void
    {
        $this->componentItems = $componentItems;
    }


}
