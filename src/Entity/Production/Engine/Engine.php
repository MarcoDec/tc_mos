<?php

namespace App\Entity\Production\Engine;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Doctrine\DBAL\Types\Production\Engine\EngineType;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Blocker;
use App\Entity\Embeddable\EmployeeEngineState;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Production\Company\Zone;
use App\Entity\Production\Engine\Attachment\EngineAttachment;
use App\Entity\Production\Engine\CounterPart\CounterPart;
use App\Entity\Production\Engine\Manufacturer\Engine as ManufacturerEngine;
use App\Entity\Production\Engine\Tool\Tool;
use App\Entity\Production\Engine\Workstation\Workstation;
use App\Entity\Traits\BarCodeTrait;
use App\Filter\RelationFilter;
//use App\Filter\SetFilter;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['brand'=>'partial', 'code'=> 'partial', 'name' => 'partial', 'serialNumber' => 'partial', 'zone.company']),
    ApiFilter(filterClass: DateFilter::class, properties: ['entryDate']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['zone', 'manufacturerEngine']),
    ApiFilter(filterClass: OrderFilter::class, properties: ['brand', 'code', 'entryDate', 'manufacturerEngine.name', 'name', 'serialNumber']),
    //ApiFilter(filterClass: SetFilter::class, properties: ['embState.state','embBlocker.state']),
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
                    'parameters' => [
                        [
                            'in' => 'path',
                            'name' => 'transition',
                            'required' => true,
                            'schema' => ['enum' => [...EmployeeEngineState::TRANSITIONS, ...Blocker::TRANSITIONS], 'type' => 'string']
                        ],
                        [
                            'in' => 'path',
                            'name' => 'workflow',
                            'required' => true,
                            'schema' => ['enum' => ['employee_engine', 'blocker'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite l\'équipement à son prochain statut de workflow'
                ],
                'path' => '/engines/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_WRITER.'\')',
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
            'groups' => ['read:engine', 'read:id', 'read:state'],
            'openapi_definition_name' => 'Engine-read',
            'skip_null_values' => false
        ]
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'engine'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE')
]
abstract class Engine extends Entity implements BarCodeInterface {
    use BarCodeTrait;

    final public const TYPES = [
        EngineType::TYPE_COUNTER_PART => CounterPart::class,
        EngineType::TYPE_TOOL => Tool::class,
        EngineType::TYPE_WORKSTATION => Workstation::class
    ];

    #[ORM\OneToMany(mappedBy: 'engine', targetEntity: EngineAttachment::class)]
    private Collection $attachments;

    #[
        ApiProperty(description: 'Marque', example: 'Apple'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:engine', 'write:engine','read:manufacturing-operation','read:engine-maintenance-event'])
    ]
    protected ?string $brand = null;

    #[
        ApiProperty(description: 'Référence', example: 'MA-1'),
        ORM\Column(length: 10, nullable: true),
        Serializer\Groups(['read:engine','read:manufacturing-operation','read:engine-maintenance-event'])
    ]
    protected ?string $code = null;

    #[
        ApiProperty(description: 'Date d\'arrivée', example: '2021-01-12'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    protected ?DateTimeImmutable $entryDate = null;

    /**
     * @var Group|null
     */
    protected $group;
    #[
        ApiProperty(description: 'Nom', example: 'Compresseur d\'air'),
        ORM\Column,
        Serializer\Groups(['read:engine', 'write:engine','read:manufacturing-operation','read:engine-maintenance-event'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Numéro de série', example: '10021'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:engine', 'write:engine','read:manufacturing-operation','read:engine-maintenance-event'])
    ]
    protected ?string $serialNumber = null;

    /**
     * @return string|null
     */
    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    /**
     * @param string|null $serialNumber
     * @return Engine
     */
    public function setSerialNumber(?string $serialNumber): Engine
    {
        $this->serialNumber = $serialNumber;
        return $this;
    }

    #[
        ApiProperty(description: 'Zone', readableLink: true),
        ORM\ManyToOne(fetch: 'EAGER'),
        Serializer\Groups(['read:engine', 'write:engine','read:manufacturing-operation','read:engine-maintenance-event'])
    ]
    protected ?Zone $zone = null;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:engine','read:manufacturing-operation'])
    ]
    private Blocker $embBlocker;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:engine','read:manufacturing-operation'])
    ]
    private EmployeeEngineState $embState;

    #[
        ApiProperty(description: 'Modele de machine', readableLink: false, example: '/api/manufacturer-engines/15'),
        ORM\ManyToOne(targetEntity: ManufacturerEngine::class, cascade: ['persist']),
        ORM\JoinColumn(onDelete: 'SET NULL'),
        Serializer\Groups(['read:engine', 'write:engine', 'read:manufacturing-operation'])
    ]
    private ?ManufacturerEngine $manufacturerEngine;

    #[
        ApiProperty(description: 'Opérateur maximum ', example: 1),
        ORM\Column(type: 'tinyint', options: ['default' => 1, 'unsigned' => true]),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private int $maxOperator = 1;

    #[
        ApiProperty(description: 'Notes'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ?string $notes = null;

    public function __construct() {
        $this->embBlocker = new Blocker();
        $this->embState = new EmployeeEngineState();
    }

    public static function getBarCodeTableNumber(): string {
        return self::ENGINE_BAR_CODE_TABLE_NUMBER;
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
    }

    final public function getBrand(): ?string {
        return $this->brand;
    }

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getEmbBlocker(): Blocker {
        return $this->embBlocker;
    }

    final public function getEmbState(): EmployeeEngineState {
        return $this->embState;
    }

    final public function getEntryDate(): ?DateTimeImmutable {
        return $this->entryDate;
    }

    final public function getGroup(): ?Group {
        return $this->group;
    }

    final public function getManufacturerEngine(): ?ManufacturerEngine {
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

    final public function getState(): string {
        return $this->embState->getState();
    }

    final public function getZone(): ?Zone {
        return $this->zone;
    }

    final public function setBlocker(string $state): self {
        $this->embBlocker->setState($state);
        return $this;
    }

    final public function setBrand(?string $brand): self {
        $this->brand = $brand;
        return $this;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setEmbBlocker(Blocker $embBlocker): self {
        $this->embBlocker = $embBlocker;
        return $this;
    }

    final public function setEmbState(EmployeeEngineState $embState): self {
        $this->embState = $embState;
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

    final public function setManufacturerEngine(?ManufacturerEngine $manufacturerEngine): self {
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

    final public function setState(string $state): self {
        $this->embState->setState($state);
        return $this;
    }

    final public function setZone(?Zone $zone): self {
        $this->zone = $zone;
        return $this;
    }

   /**
    * @return Collection
    */
   public function getAttachments(): Collection
   {
      return $this->attachments;
   }

   /**
    * @param Collection $attachments
    */
   public function setAttachments(Collection $attachments): void
   {
      $this->attachments = $attachments;
   }


}
