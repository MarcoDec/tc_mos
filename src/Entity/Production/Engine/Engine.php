<?php

namespace App\Entity\Production\Engine;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Production\Engine\EngineType;
use App\Entity\Embeddable\EmployeeEngineCurrentPlace;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Production\Company\Zone;
use App\Entity\Production\Engine\CounterPart\CounterPart;
use App\Entity\Production\Engine\Manufacturer\Engine as ManufacturerEngine;
use App\Entity\Production\Engine\Tool\Tool;
use App\Entity\Production\Engine\Workstation\Workstation;
use App\Entity\Traits\BarCodeTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Équipement',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les équipements',
                    'summary' => 'Récupère les équipements',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un équipement',
                    'summary' => 'Supprime un équipement',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un équipement',
                    'summary' => 'Récupère un équipement',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un équipement',
                    'summary' => 'Modifie un équipement',
                ]
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite l\'équipement à son prochain statut de workflow',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'transition',
                        'required' => true,
                        'schema' => [
                            'enum' => EmployeeEngineCurrentPlace::TRANSITIONS,
                            'type' => 'string'
                        ]
                    ]],
                    'requestBody' => null,
                    'summary' => 'Transite le l\'équipement à son prochain statut de workflow'
                ],
                'path' => '/engines/{id}/promote/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')',
                'validate' => false
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:engine'],
            'openapi_definition_name' => 'Engine-write'
        ],
        normalizationContext: [
            'enable_max_depth' => true,
            'groups' => ['read:current_place', 'read:engine', 'read:id'],
            'openapi_definition_name' => 'Engine-read',
            'skip_null_values' => false
        ],
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'engine_type'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE')
]
abstract class Engine extends Entity implements BarCodeInterface {
    use BarCodeTrait;

    public const TYPES = [
        EngineType::TYPE_COUNTER_PART => CounterPart::class,
        EngineType::TYPE_TOOL => Tool::class,
        EngineType::TYPE_WORKSTATION => Workstation::class
    ];

    /**
     * @var Group|null
     */
    protected $group;

    #[
        ApiProperty(description: 'Marque', example: 'Apple'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ?string $brand = null;

    #[
        ApiProperty(description: 'Référence', example: 'MA-1'),
        ORM\Column(length: 10),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Compagnie', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:engine'])
    ]
    private ?Company $company = null;

    #[
        ApiProperty(description: 'Statut', example: 'end_of_life'),
        ORM\Embedded(EmployeeEngineCurrentPlace::class),
        Serializer\Groups(['read:engine'])
    ]
    private EmployeeEngineCurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Date d\'arrivée', example: '2021-01-12'),
        Assert\Date,
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ?DateTimeImmutable $entryDate = null;

    #[
        ORM\OneToOne(mappedBy: 'engine', cascade: ['remove', 'persist'], fetch: 'EAGER'),
        Serializer\Groups(['read:engine', 'write:engine']),
        Serializer\MaxDepth(1)
    ]
    private ManufacturerEngine $manufacturerEngine;

    #[
        ApiProperty(description: 'Opérateur maximum ', example: 1),
        ORM\Column(type: 'tinyint', options: ['default' => 1, 'unsigned' => true]),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private int $maxOperator = 1;

    #[
        ApiProperty(description: 'Nom', example: 'Machine'),
        ORM\Column(length: 80),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Notes'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Zone', readableLink: false, example: '/api/zones/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ?Zone $zone = null;

    public function __construct() {
        $this->currentPlace = new EmployeeEngineCurrentPlace();
        $this->manufacturerEngine = new ManufacturerEngine($this);
    }

    public static function getBarCodeTableNumber(): string {
        return self::ENGINE_BAR_CODE_TABLE_NUMBER;
    }

    final public function getBrand(): ?string {
        return $this->brand;
    }

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getCurrentPlace(): EmployeeEngineCurrentPlace {
        return $this->currentPlace;
    }

    final public function getEntryDate(): ?DateTimeImmutable {
        return $this->entryDate;
    }

    final public function getGroup(): ?Group {
        return $this->group;
    }

    final public function getManufacturerEngine(): ManufacturerEngine {
        return $this->manufacturerEngine;
    }

    final public function getMaxOperator(): int {
        return $this->maxOperator;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getZone(): ?Zone {
        return $this->zone;
    }

    final public function setBrand(?string $brand): self {
        $this->brand = $brand;
        return $this;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setCurrentPlace(EmployeeEngineCurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
        return $this;
    }

    final public function setEntryDate(?DateTimeImmutable $entryDate): self {
        $this->entryDate = $entryDate;
        return $this;
    }

    final public function setGroup(?Group $group): self {
        $this->group = $group;
        return $this;
    }

    final public function setManufacturerEngine(ManufacturerEngine $manufacturerEngine): self {
        $this->manufacturerEngine = $manufacturerEngine;
        return $this;
    }

    final public function setMaxOperator(int $maxOperator): self {
        $this->maxOperator = $maxOperator;
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

    final public function setZone(?Zone $zone): self {
        $this->zone = $zone;
        return $this;
    }
}
