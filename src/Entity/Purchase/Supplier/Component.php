<?php

namespace App\Entity\Purchase\Supplier;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Logistics\Incoterms;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Entity;
use Symfony\Component\Serializer\Annotation as Serializer;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Purchase\Component\Component as TechnicalSheet;
use App\Entity\Traits\RefTrait;
use App\Filter\RelationFilter;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'component' => 'name',
        'supplier' => 'name'
    ]),
    ApiResource(
        description: 'Composant',
        collectionOperations: [
            'get' => [
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
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifier un composant',
                    'summary' => 'Modifier un composant',
                ]
            ],
        ],
        shortName: "SupplierComponent",
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:supplier-component', 'write:supplier', 'write:component', 'write:measure', 'write:incoterms', 'write:ref'],
            'openapi_definition_name' => 'SupplierComponent-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:supplier-component', 'read:ref', 'read:supplier', 'read:component', 'read:measure', 'read:incoterms'],
            'openapi_definition_name' => 'SupplierComponent-read'
        ]
    ),
    ORM\Entity,
    ORM\Table(name: "component_supplier")
]
class Component extends Entity
{
    use RefTrait;

    #[
        ApiProperty(description: 'Référence', example: "DH544G"),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:ref', 'write:ref'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Composant', required: false, readableLink: false, example: '/api/components/4'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: TechnicalSheet::class),
        Serializer\Groups(['read:component', 'write:component'])
    ]
    private ?TechnicalSheet $component;

    #[
        ApiProperty(description: 'Poids du cuivre', example: '3'),
        ORM\Embedded(Measure::class),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private Measure $copperWeight;

    #[
        ApiProperty(description: 'Temps de livraison', example: '7'),
        ORM\Embedded(Measure::class),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private Measure $deliveryTime;

    #[
        ApiProperty(description: 'Incoterms', required: false),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Incoterms::class),
        Serializer\Groups(['read:incoterms', 'write:incoterms'])
    ]
    private ?Incoterms $incoterms;

    #[
        ApiProperty(description: 'Indice', required: true, example: '0'),
        ORM\Column(type: 'string', options: ['default' => '0'], name: 'supplier_component_index'),
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private string $index = '0';

    #[
        ApiProperty(description: 'MOQ (Minimal Order Quantity)', required: true, example: '1'),
        ORM\Column(options: ['default' => 1, 'unsigned' => true], type: 'float'),
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private float $moq = 1;

    #[
        ApiProperty(description: 'Conditionnement', required: true, example: 1),
        ORM\Column(options: ['default' => 1, 'unsigned' => true], type: 'integer'),
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private int $packaging = 1;

    #[
        ApiProperty(description: 'Type de packaging', required: false, example: 'Palette'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private ?string $packagingKind = null;

    #[
        ApiProperty(description: 'Proportion', required: true, example: '99'),
        ORM\Column(options: ['default' => 100, 'unsigned' => true], type: 'float'),
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private float $proportion = 100;

    #[
        ApiProperty(description: 'Fournisseur', required: false, readableLink: false, example: '/api/suppliers/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Supplier::class),
        Serializer\Groups(['read:supplier', 'write:supplier'])
    ]
    private ?Supplier $supplier;

    public function __construct() {
        $this->copperWeight = new Measure();
        $this->deliveryTime = new Measure();
    }

    public function getCopperWeight(): Measure
    {
        return $this->copperWeight;
    }

    public function setCopperWeight(Measure $copperWeight): self
    {
        $this->copperWeight = $copperWeight;

        return $this;
    }

    public function getDeliveryTime(): Measure
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(Measure $deliveryTime): self
    {
        $this->deliveryTime = $deliveryTime;

        return $this;
    }

    public function getIndex(): ?string
    {
        return $this->index;
    }

    public function setIndex(string $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function getMoq(): ?float
    {
        return $this->moq;
    }

    public function setMoq(float $moq): self
    {
        $this->moq = $moq;

        return $this;
    }

    public function getPackaging(): ?int
    {
        return $this->packaging;
    }

    public function setPackaging(int $packaging): self
    {
        $this->packaging = $packaging;

        return $this;
    }

    public function getPackagingKind(): ?string
    {
        return $this->packagingKind;
    }

    public function setPackagingKind(?string $packagingKind): self
    {
        $this->packagingKind = $packagingKind;

        return $this;
    }

    public function getProportion(): ?float
    {
        return $this->proportion;
    }

    public function setProportion(float $proportion): self
    {
        $this->proportion = $proportion;

        return $this;
    }

    public function getComponent(): ?TechnicalSheet
    {
        return $this->component;
    }

    public function setComponent(?TechnicalSheet $component): self
    {
        $this->component = $component;

        return $this;
    }

    public function getIncoterms(): ?Incoterms
    {
        return $this->incoterms;
    }

    public function setIncoterms(?Incoterms $incoterms): self
    {
        $this->incoterms = $incoterms;

        return $this;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }
   
}
