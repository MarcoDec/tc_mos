<?php

namespace App\Entity\Production\Engine;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Production\Engine\CurrentPlace;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Production\Company\Zone;
use App\Entity\Production\Engine\Manufacturer\Engine as ManufacturerEngine;
use App\Entity\Production\Engine\Tool\Tool;
use App\Entity\Production\Engine\Workstation\Workstation;
use App\Entity\Traits\BarCodeTrait;
use App\Entity\Traits\CompanyTrait;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\NotesTrait;
use App\Entity\Traits\RefTrait;
use App\Filter\RelationFilter;
use DatetimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'manufacturerEngine' => 'id',
    ]),
    ApiResource(
        description: 'Machine',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les machines',
                    'summary' => 'Récupère les machines',
                ]
            ]
        ],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une machine',
                    'summary' => 'Récupère une machine',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une machine',
                    'summary' => 'Modifie une machine',
                ]
            ],
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une machine',
                    'summary' => 'Supprime une machine',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ]

        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        shortName: 'ManufacturerEngine',
        denormalizationContext: [
            'groups' => ['write:engine', 'write:zone', 'write:ref', 'write:manufacturer', 'write:name', 'write:current_place'],
            'openapi_definition_name' => 'Engine-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:engine', 'read:zone', 'read:current_place', 'read:name', 'read:ref', 'read:manufacturer'],
            'openapi_definition_name' => 'Engine-read'
        ],
    ),
    ORM\Entity,
    ORM\DiscriminatorColumn(name: 'type', type: 'string'),
    ORM\DiscriminatorMap(Engine::TYPES),
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\MappedSuperclass
]
abstract class Engine extends Entity implements BarCodeInterface {
    use BarCodeTrait;
    use CompanyTrait;
    use NameTrait, RefTrait {
        RefTrait::__toString insteadof NameTrait;
    }
    use NotesTrait;

    public const TYPES = ['tool' => Tool::class, 'workstation' => Workstation::class];

    #[
        ApiProperty(description: 'Statut', example: 'end_of_life'),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:current_place', 'write:current_place'])
    ]
    protected CurrentPlace $currentPlace;

    /**
     * @var Group|null
     */
    protected $group;

    #[
        ApiProperty(description: 'Marque', required: true, example: 'Apple'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ?string $brand = null;

    #[
        ApiProperty(description: 'Date d\'arrivée', example: '2021-01-12'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ?DatetimeInterface $entryDate = null;

    #[
        ORM\OneToOne(targetEntity: ManufacturerEngine::class, inversedBy: 'engine', ),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ManufacturerEngine $manufacturerEngine;

    #[
        ApiProperty(description: 'Opérateur maximum ', required: true, example: 1),
        ORM\Column(type: 'tinyint', options: ['default' => 1, 'unsigned' => true]),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private int $maxOperator = 1;

    #[
        ApiProperty(description: 'Zone', required: false, readableLink: false, example: '/api/zones/1'),
        ORM\ManyToOne(targetEntity: Zone::class),
        Serializer\Groups(['read:zone', 'write:zone'])
    ]
    private ?Zone $zone = null;

    public function __construct() {
        $this->currentPlace = new CurrentPlace();
        $this->manufacturerEngine = (new ManufacturerEngine())->setEngine($this);
    }

    public static function getBarCodeTableNumber(): string {
        return self::ENGINE_BAR_CODE_TABLE_NUMBER;
    }

    public function getBrand(): ?string {
        return $this->brand;
    }

    public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    public function getEntryDate(): ?DateTimeInterface {
        return $this->entryDate;
    }

    final public function getGroup(): ?Group {
        return $this->group;
    }

    public function getManufacturerEngine(): ManufacturerEngine {
        return $this->manufacturerEngine;
    }

    public function getMaxOperator(): int {
        return $this->maxOperator;
    }

    public function getZone(): ?Zone {
        return $this->zone;
    }

    public function setBrand(?string $brand): self {
        $this->brand = $brand;

        return $this;
    }

    public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;

        return $this;
    }

    public function setEntryDate(?DateTimeInterface $entryDate): self {
        $this->entryDate = $entryDate;

        return $this;
    }

    final public function setGroup(?Group $group): self {
        $this->group = $group;
        return $this;
    }

    public function setManufacturerEngine(ManufacturerEngine $manufacturerEngine): self {
        $this->manufacturerEngine = $manufacturerEngine;

        return $this;
    }

    public function setMaxOperator(int $maxOperator): self {
        $this->maxOperator = $maxOperator;

        return $this;
    }

    public function setZone(?Zone $zone): self {
        $this->zone = $zone;

        return $this;
    }
}
