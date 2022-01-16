<?php

namespace App\Entity\Production\Manufacturing;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Production\Manufacturing\Operation\CurrentPlace;
use App\Entity\Entity;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Production\Company\Zone;
use App\Entity\Production\Engine\Workstation\Workstation;
use App\Entity\Project\Operation\Operation as PrimaryOperation;
use App\Entity\Traits\NotesTrait;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Opération de production',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les opérations de production',
                    'summary' => 'Récupère les opérations de production',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Crée une opération de production',
                    'summary' => 'Crée une opération de production',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une opération de production',
                    'summary' => 'Supprime une opération de production',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une opération de production',
                    'summary' => 'Récupère une opération de production',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une opération de production',
                    'summary' => 'Modifie une opération de production',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ],
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:notes', 'write:current_place', 'write:manufacturing_operation'],
            'openapi_definition_name' => 'ManufacturingOperation-write'
        ],
        normalizationContext: [
            'groups' => ['read:namnotese', 'read:id', 'read:current_place', 'read:manufacturing_operation'],
            'openapi_definition_name' => 'ManufacturingOperation-read'
        ]
    ),
    ORM\Entity,
]
class Operation extends Entity {
    use NotesTrait;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Lorem ipsum'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:notes', 'write:notes'])
    ]
    protected ?string $notes = null;

    #[
        ApiProperty(description: 'Statut', required: true, example: CurrentPlace::WF_PLACE_EXEMPTIONED),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:current_place', 'write:current_place'])
    ]
    private CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Opération', required: false, example: '/api/project-operations/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: PrimaryOperation::class),
        Serializer\Groups(['read:manufacturing_operation', 'write:manufacturing_operation'])
    ]
    private ?PrimaryOperation $operation = null;

    /**
     * @var Collection<int, Employee>
     */
    #[
        ApiProperty(description: 'Opérateurs', required: false, example: ['/api/employees/1', '/api/employees/2']),
        ORM\ManyToOne(fetch: 'EXTRA_LAZY', targetEntity: Employee::class, inversedBy: 'operations'),
        Serializer\Groups(['read:manufacturing_operation', 'write:manufacturing_operation'])
    ]
    private Collection $operators;

    #[
        ApiProperty(description: 'Commande', required: false, example: '/api/manufacturing-orders/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Order::class),
        Serializer\Groups(['read:manufacturing_operation', 'write:manufacturing_operation'])
    ]
    private ?Order $order = null;

    #[
        ApiProperty(description: 'Personne en charge', required: false, example: '/api/employee/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Employee::class),
        Serializer\Groups(['read:manufacturing_operation', 'write:manufacturing_operation'])
    ]
    private ?Employee $pic = null;

    #[
        ApiProperty(description: 'Date de départ', required: true, example: '2022-24-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:manufacturing_operation', 'write:manufacturing_operation'])
    ]
    private DateTimeInterface $startedDate;

    #[
        ApiProperty(description: 'Personne en charge', required: false, example: '/api/workstations/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Workstation::class),
        Serializer\Groups(['read:manufacturing_operation', 'write:manufacturing_operation'])
    ]
    private ?Workstation $workstation = null;

    #[
        ApiProperty(description: 'Personne en charge', required: false, example: '/api/zones/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Zone::class),
        Serializer\Groups(['read:manufacturing_operation', 'write:manufacturing_operation'])
    ]
    private ?Zone $zone = null;

    public function __construct() {
        $this->currentPlace = new CurrentPlace();
        $this->operators = new ArrayCollection();
    }

    final public function addOperator(Employee $operator): self {
        if (!$this->operators->contains($operator)) {
            $this->operators->add($operator);
            $operator->addOperation($this);
        }
        return $this;
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getOperation(): ?PrimaryOperation {
        return $this->operation;
    }

    /**
     * @return Collection<int, Employee>
     */
    final public function getOperators(): Collection {
        return $this->operators;
    }

    final public function getOrder(): ?Order {
        return $this->order;
    }

    final public function getPic(): ?Employee {
        return $this->pic;
    }

    final public function getStartedDate(): DateTimeInterface {
        return $this->startedDate;
    }

    final public function getWorkstation(): ?Workstation {
        return $this->workstation;
    }

    final public function getZone(): ?Zone {
        return $this->zone;
    }

    final public function removeOperator(Employee $operator): self {
        if ($this->operators->contains($operator)) {
            $this->operators->removeElement($operator);
            $operator->removeOperation($this);
        }
        return $this;
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
        return $this;
    }

    final public function setOperation(?PrimaryOperation $operation): self {
        $this->operation = $operation;
        return $this;
    }

    final public function setOrder(?Order $order): self {
        $this->order = $order;
        return $this;
    }

    final public function setPic(?Employee $pic): self {
        $this->pic = $pic;
        return $this;
    }

    final public function setStartedDate(DateTimeInterface $startedDate): self {
        $this->startedDate = $startedDate;
        return $this;
    }

    final public function setWorkstation(?Workstation $workstation): self {
        $this->workstation = $workstation;
        return $this;
    }

    final public function setZone(?Zone $zone): self {
        $this->zone = $zone;
        return $this;
    }
}
