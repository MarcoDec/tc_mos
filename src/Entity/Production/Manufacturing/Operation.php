<?php

namespace App\Entity\Production\Manufacturing;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Closer;
use App\Entity\Embeddable\ComponentManufacturingOperationState;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Entity\Production\Company\Zone;
use App\Entity\Production\Engine\Workstation\Workstation;
use App\Entity\Project\Operation\Operation as PrimaryOperation;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

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
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une opération de production',
                    'summary' => 'Modifie une opération de production',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite l\'opération à son prochain statut de workflow',
                    'parameters' => [
                        [
                            'in' => 'path',
                            'name' => 'transition',
                            'required' => true,
                            'schema' => ['enum' => [...ComponentManufacturingOperationState::TRANSITIONS, ...Closer::TRANSITIONS], 'type' => 'string']
                        ],
                        [
                            'in' => 'path',
                            'name' => 'workflow',
                            'required' => true,
                            'schema' => ['enum' => ['component_manufacturing_operation', 'closer'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite l\'opération à son prochain statut de workflow'
                ],
                'path' => '/manufacturing-operations/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_WRITER.'\')',
                'validate' => false
            ]
        ],
        shortName: 'ManufacturingOperation',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:manufacturing-operation', 'write:measure'],
            'openapi_definition_name' => 'ManufacturingOperation-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:manufacturing-operation', 'read:state', 'read:measure'],
            'openapi_definition_name' => 'ManufacturingOperation-read',
            'skip_null_values' => false
        ],
       paginationClientEnabled: true
    ),
    ORM\Entity,
    ORM\Table(name: 'manufacturing_operation')
]
class Operation extends Entity implements MeasuredInterface {
    #[
        ApiProperty(description: 'Quantité actuelle', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:manufacturing-operation', 'write:manufacturing-operation'])
    ]
    private Measure $actualQuantity;
   #[
      ApiProperty(description: 'Durée', example: 'HH:mm:ss'),
      ORM\Column(type: 'string', nullable: true),
      Serializer\Groups(['read:manufacturing-operation', 'write:manufacturing-operation'])
   ]
   private string|null $duration = null;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:manufacturing-operation'])
    ]
    private Closer $embBlocker;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:manufacturing-operation'])
    ]
    private ComponentManufacturingOperationState $embState;

    #[
        ApiProperty(description: 'Notes', example: 'Lorem ipsum'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:manufacturing-operation', 'write:manufacturing-operation'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Opération'),
        ORM\ManyToOne(inversedBy: 'operations'),
        Serializer\Groups(['read:manufacturing-operation', 'write:manufacturing-operation'])
    ]
    private ?PrimaryOperation $operation = null;

    /** @var Collection<int, Employee> */
    #[
        ApiProperty(description: 'Opérateurs'),
        ORM\ManyToMany(targetEntity: Employee::class),
        Serializer\Groups(['read:manufacturing-operation', 'write:manufacturing-operation'])
    ]
    private Collection $operators;

    #[
        ApiProperty(description: 'Commande'),
        ORM\ManyToOne,
        Serializer\Groups(['read:manufacturing-operation', 'write:manufacturing-operation'])
    ]
    private ?Order $order = null;

    #[
        ApiProperty(description: 'Personne en charge', readableLink: false, example: '/api/employees/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:manufacturing-operation', 'write:manufacturing-operation'])
    ]
    private ?Employee $pic = null;

    #[
        ApiProperty(description: 'Quantité produite', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:manufacturing-operation', 'write:manufacturing-operation'])
    ]
    private Measure $quantityProduced;

    #[
        ApiProperty(description: 'Date de démarrage', example: '2022-03-24'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:manufacturing-operation', 'write:manufacturing-operation'])
    ]
    private ?DateTimeImmutable $startedDate = null;

    #[
        ApiProperty(description: 'Poste de travail'),
        ORM\ManyToOne,
        Serializer\Groups(['read:manufacturing-operation', 'write:manufacturing-operation'])
    ]
    private ?Workstation $workstation = null;

    #[
        ApiProperty(description: 'Zone', readableLink: false, example: '/api/zones/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:manufacturing-operation', 'write:manufacturing-operation'])
    ]
    private ?Zone $zone = null;

    public function __construct() {
        $this->actualQuantity = new Measure();
        $this->embBlocker = new Closer();
        $this->embState = new ComponentManufacturingOperationState();
        $this->operators = new ArrayCollection();
        $this->quantityProduced = new Measure();
    }

    final public function addOperator(Employee $operator): self {
        if (!$this->operators->contains($operator)) {
            $this->operators->add($operator);
        }
        return $this;
    }

    final public function getActualQuantity(): Measure {
        return $this->actualQuantity;
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
    }

    final public function getEmbBlocker(): Closer {
        return $this->embBlocker;
    }

    final public function getEmbState(): ComponentManufacturingOperationState {
        return $this->embState;
    }

    final public function getNotes(): ?string {
        return $this->notes;
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

    final public function getQuantityProduced(): Measure {
        return $this->quantityProduced;
    }

    final public function getRef(): ?string {
        return $this->order?->getRef();
    }

    final public function getStartedDate(): ?DateTimeImmutable {
        return $this->startedDate;
    }

    final public function getState(): string {
        return $this->embState->getState();
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
        }
        return $this;
    }

    final public function setActualQuantity(Measure $actualQuantity): self {
        $this->actualQuantity = $actualQuantity;
        return $this;
    }

    final public function setBlocker(string $state): self {
        $this->embBlocker->setState($state);
        return $this;
    }

    final public function setEmbBlocker(Closer $embBlocker): self {
        $this->embBlocker = $embBlocker;
        return $this;
    }

    final public function setEmbState(ComponentManufacturingOperationState $embState): self {
        $this->embState = $embState;
        return $this;
    }

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
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

    final public function setQuantityProduced(Measure $quantityProduced): self {
        $this->quantityProduced = $quantityProduced;
        return $this;
    }

    final public function setStartedDate(?DateTimeImmutable $startedDate): self {
        $this->startedDate = $startedDate;
        return $this;
    }

    final public function setState(string $state): self {
        $this->embState->setState($state);
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

   /**
    * @return string|null
    */
   public function getDuration(): ?string
   {
      return $this->duration;
   }

   /**
    * @param string|null $duration
    */
   public function setDuration(?string $duration): void
   {
      $this->duration = $duration;
   }

   public function getMeasures(): array
   {
      return [$this->actualQuantity, $this->quantityProduced];
   }

   public function getUnit(): ?Unit
   {
      return $this->order->getProduct()->getUnit();
   }
}
