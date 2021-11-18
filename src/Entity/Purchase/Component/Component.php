<?php

namespace App\Entity\Purchase\Component;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Unit;
use App\Entity\Traits\CodeTrait;
use App\Entity\Traits\NameTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Composant',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:code', 'read:component', 'read:id', 'read:name'],
                    'openapi_definition_name' => 'Component-read'
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
                ]
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
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:code', 'write:create', 'write:name'],
            'openapi_definition_name' => 'Component-write'
        ],
        normalizationContext: [
            'groups' => ['read:code', 'read:component', 'read:component-details', 'read:id', 'read:name'],
            'openapi_definition_name' => 'Component-details-read'
        ],
        validationGroups: ['write:code', 'write:create', 'write:name']
    ),
    ORM\Entity
]
class Component extends Entity {
    use CodeTrait;
    use NameTrait;

    #[
        ApiProperty(description: 'Référence interne', required: true, example: 'FIX-1'),
        Assert\Length(max: 10, groups: ['write:create']),
        Assert\NotBlank(groups: ['write:create']),
        ORM\Column(length: 10),
        Serializer\Groups(['read:code', 'write:code'])
    ]
    protected ?string $code = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: '2702 SCOTCH ADHESIF PVC T2 19MMX33M NOIR'),
        Assert\NotBlank(groups: ['write:create']),
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Poids cuivre', example: 0),
        Assert\PositiveOrZero(groups: ['write:create']),
        ORM\Column(options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:component', 'write:create'])
    ]
    private float $copperWeight = 0;

    #[
        ApiProperty(description: 'Date de fin de vie', required: true, example: '2021-11-18'),
        Assert\Date(groups: ['write:create']),
        Assert\NotBlank(groups: ['write:create']),
        ORM\Column(type: 'date_immutable'),
        Serializer\Groups(['read:component-details', 'write:create'])
    ]
    private ?DateTimeImmutable $endOfLife = null;

    #[
        ApiProperty(description: 'Famille', readableLink: false, required: true, example: '/api/component-families/1'),
        Assert\NotBlank(groups: ['write:create']),
        ORM\ManyToOne,
        Serializer\Groups(['read:component', 'write:create'])
    ]
    private ?Family $family = null;

    #[
        ApiProperty(description: 'Volume prévisionnel', example: 2_229_116),
        Assert\PositiveOrZero(groups: ['write:create']),
        ORM\Column(options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:component', 'write:create'])
    ]
    private float $forecastVolume = 0;

    #[
        ApiProperty(description: 'Indice', required: true, example: '1'),
        Assert\Length(max: 5),
        Assert\NotBlank(groups: ['write:create']),
        ORM\Column(length: 5),
        Serializer\Groups(['read:component', 'write:create'])
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
        ORM\Column,
        Serializer\Groups(['read:component', 'write:create'])
    ]
    private ?string $manufacturer = null;

    #[
        ApiProperty(description: 'Référence fabricant', required: true, example: '103078'),
        Assert\NotBlank(groups: ['write:create']),
        ORM\Column,
        Serializer\Groups(['read:component', 'write:create'])
    ]
    private ?string $manufacturerCode = null;

    #[
        ApiProperty(description: 'Stock minimum', example: 221_492),
        Assert\PositiveOrZero(groups: ['write:create']),
        ORM\Column(options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:component', 'write:create'])
    ]
    private float $minStock = 0;

    #[
        ApiProperty(description: 'Besoin de join', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:component-details'])
    ]
    private bool $needGasket = false;

    #[
        ApiProperty(description: 'Notes'),
        ORM\Column(type: 'text'),
        Serializer\Groups(['read:component-details', 'write:create'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Info commande'),
        ORM\Column(type: 'text'),
        Serializer\Groups(['read:component-details', 'write:create'])
    ]
    private ?string $orderInfo = null;

    #[
        ApiProperty(description: 'Parent', readableLink: false, example: '/api/components/1'),
        ORM\ManyToOne(targetEntity: self::class),
        Serializer\Groups(['read:component'])
    ]
    private ?self $parent = null;

    #[
        ApiProperty(description: 'Unité', readableLink: false, required: true, example: '/api/units/1'),
        Assert\NotBlank(groups: ['write:create']),
        ORM\ManyToOne,
        Serializer\Groups(['read:component', 'write:create'])
    ]
    private ?Unit $unit = null;

    #[
        ApiProperty(description: 'Poids', example: 3.0378),
        Assert\PositiveOrZero(groups: ['write:create']),
        ORM\Column(options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:component', 'write:create'])
    ]
    private float $weight = 0;

    final public function getCopperWeight(): float {
        return $this->copperWeight;
    }

    final public function getEndOfLife(): ?DateTimeImmutable {
        return $this->endOfLife;
    }

    final public function getFamily(): ?Family {
        return $this->family;
    }

    final public function getForecastVolume(): float {
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

    final public function getMinStock(): float {
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

    final public function getUnit(): ?Unit {
        return $this->unit;
    }

    final public function getWeight(): float {
        return $this->weight;
    }

    final public function isManagedStock(): bool {
        return $this->managedStock;
    }

    final public function isNeedGasket(): bool {
        return $this->needGasket;
    }

    final public function setCopperWeight(float $copperWeight): self {
        $this->copperWeight = $copperWeight;
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

    final public function setForecastVolume(float $forecastVolume): self {
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

    final public function setMinStock(float $minStock): self {
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

    final public function setUnit(?Unit $unit): self {
        $this->unit = $unit;
        return $this;
    }

    final public function setWeight(float $weight): self {
        $this->weight = $weight;
        return $this;
    }
}
