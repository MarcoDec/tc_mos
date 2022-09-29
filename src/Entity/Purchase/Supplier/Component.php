<?php

namespace App\Entity\Purchase\Supplier;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Logistics\Incoterms;
use App\Entity\Management\Unit;
use App\Entity\Purchase\Component\Component as TechnicalSheet;
use App\Filter\RelationFilter;
use App\Repository\Purchase\Supplier\ComponentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['component', 'supplier']),
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
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifier un composant',
                    'summary' => 'Modifier un composant',
                ]
            ],
        ],
        shortName: 'SupplierComponent',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:supplier-component'],
            'openapi_definition_name' => 'SupplierComponent-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:supplier-component'],
            'openapi_definition_name' => 'SupplierComponent-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity(repositoryClass: ComponentRepository::class),
    ORM\Table(name: 'supplier_component')
]
class Component extends Entity {
    #[
        ApiProperty(description: 'Référence', example: 'DH544G'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Composant', readableLink: false, example: '/api/components/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private ?TechnicalSheet $component = null;

    #[
        ApiProperty(description: 'Poids cuivre', openapiContext: ['$ref' => '#/components/schemas/Measure-linear-density']),
        ORM\Embedded,
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private Measure $copperWeight;

    #[
        ApiProperty(description: 'Temps de livraison', openapiContext: ['$ref' => '#/components/schemas/Measure-duration']),
        ORM\Embedded,
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private Measure $deliveryTime;

    #[
        ApiProperty(description: 'Incoterms', readableLink: false, example: '/api/incoterms/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private ?Incoterms $incoterms = null;

    #[
        ApiProperty(description: 'Indice', example: '0'),
        ORM\Column(name: '`index`', options: ['default' => '0']),
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private string $index = '0';

    #[
        ApiProperty(description: 'MOQ (Minimal Order Quantity)', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private Measure $moq;

    #[
        ApiProperty(description: 'Conditionnement', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private Measure $packaging;

    #[
        ApiProperty(description: 'Type de packaging', example: 'Palette'),
        ORM\Column(length: 30, nullable: true),
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private ?string $packagingKind = null;

    #[
        ApiProperty(description: 'Proportion', example: '99'),
        ORM\Column(options: ['default' => 100, 'unsigned' => true]),
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private float $proportion = 100;

    #[
        ApiProperty(description: 'Fournisseur', readableLink: false, example: '/api/suppliers/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private ?Supplier $supplier = null;

    public function __construct() {
        $this->copperWeight = new Measure();
        $this->deliveryTime = new Measure();
        $this->moq = new Measure();
        $this->packaging = new Measure();
    }

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getComponent(): ?TechnicalSheet {
        return $this->component;
    }

    final public function getCopperWeight(): Measure {
        return $this->copperWeight;
    }

    final public function getDeliveryTime(): Measure {
        return $this->deliveryTime;
    }

    final public function getIncoterms(): ?Incoterms {
        return $this->incoterms;
    }

    final public function getIndex(): string {
        return $this->index;
    }

    final public function getMoq(): Measure {
        return $this->moq;
    }

    final public function getPackaging(): Measure {
        return $this->packaging;
    }

    final public function getPackagingKind(): ?string {
        return $this->packagingKind;
    }

    final public function getProportion(): float {
        return $this->proportion;
    }

    final public function getSupplier(): ?Supplier {
        return $this->supplier;
    }

    final public function getUnit(): ?Unit {
        return $this->component?->getUnit();
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setComponent(?TechnicalSheet $component): self {
        $this->component = $component;
        return $this;
    }

    final public function setCopperWeight(Measure $copperWeight): self {
        $this->copperWeight = $copperWeight;
        return $this;
    }

    final public function setDeliveryTime(Measure $deliveryTime): self {
        $this->deliveryTime = $deliveryTime;
        return $this;
    }

    final public function setIncoterms(?Incoterms $incoterms): self {
        $this->incoterms = $incoterms;
        return $this;
    }

    final public function setIndex(string $index): self {
        $this->index = $index;
        return $this;
    }

    final public function setMoq(Measure $moq): self {
        $this->moq = $moq;
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

    final public function setProportion(float $proportion): self {
        $this->proportion = $proportion;
        return $this;
    }

    final public function setSupplier(?Supplier $supplier): self {
        $this->supplier = $supplier;
        return $this;
    }
}
