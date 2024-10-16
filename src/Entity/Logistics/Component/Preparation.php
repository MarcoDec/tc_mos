<?php

namespace App\Entity\Logistics\Component;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Logistics\Component\State;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Blocker;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Logistics\Warehouse\Warehouse;
use App\Entity\Purchase\Component\Component;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Préparation de Composant',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les demandes de préparation de Composant',
                    'summary' => 'Récupère les demandes de préparation de Composant',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Ajoute une demande de préparation de Composant',
                    'summary' => 'Ajoute une demande de préparation de Composant'
                ]
                ]
        ],
        itemOperations: [
            'get' => NO_ITEM_GET_OPERATION,
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite la préparation à son prochain statut de workflow',
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
                            'schema' => ['enum' => ['preparation','blocker'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite le la préparation à son prochain statut de workflow'
                ],
                'path' => '/preparation/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')',
                'validate' => false
            ],
            'patch',
            'delete'
        ],
        shortName: 'ComponentPreparation',
        denormalizationContext: [
            'groups' => ['write:component-preparation', 'write:measure'],
            'openapi_definition_name' => 'ComponentPreparation-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:component-preparation', 'read:measure'],
            'openapi_definition_name' => 'ComponentPreparation-read'
        ],
        paginationClientEnabled: true
    ),
    \Doctrine\ORM\Mapping\Entity
]
class Preparation extends Entity {
     #[
         ApiProperty(description: 'Employé ayant fait la demande de Préparation', readableLink: false, example: '/api/employees/1'),
         ManyToOne(targetEntity: Employee::class),
         Groups(['write:component-preparation', 'read:component-preparation'])
     ]
    private Employee $askedBy;

     #[
         ApiProperty(description: 'Composant demandé'),
         ManyToOne(targetEntity: Component::class, inversedBy: 'preparationComponents'),
         Groups(['write:component-preparation', 'read:component-preparation'])
     ]
    private Component $component;

     #[
        ApiProperty(description: 'Date de la demande', example: '2023-05-31'),
        Column(options: ['default' => 'CURRENT_TIMESTAMP'] ,type: 'datetime_immutable'),
        Groups(['write:component-preparation', 'read:component-preparation'])
     ]
    private \DateTimeImmutable $requestDate;

     #[
         ApiProperty(description: 'Date d\execution de la demande', example: '2023-06-01'),
         Column(nullable: true, type: 'datetime'),
         Groups(['write:component-preparation', 'read:component-preparation'])
     ]
    private \DateTimeInterface $realizationDate;

    #[
        ApiProperty(description: 'Localisation finale', example: 'étagère 3'),
        Column(length: 255, nullable: true, type: 'string'),
        Groups(['write:component-preparation', 'read:component-preparation'])
    ]
    private string $targetLocation;

     #[
         ApiProperty(description: 'Priorité de la demande', example: '1'),
         Column(options: ['default' => 1], type: 'integer'),
         Groups(['write:component-preparation', 'read:component-preparation'])
     ]
    private int $priority = 1;

     #[
         ApiProperty(description: 'Quantité requise'),
         Embedded,
         Groups(['write:component-preparation', 'read:component-preparation'])
     ]
    private Measure $requestedQuantity;

    #[
        ApiProperty(description: 'Quantité transférée sur le poste'),
        Embedded,
        Groups(['write:component-preparation', 'read:component-preparation'])
    ]
    private Measure $sentQuantity;

     #[
         ApiProperty(description: 'Opérateur ayant réalisé la préparation', example: '/api/employees/2'),
         ManyToOne(targetEntity: Employee::class),
         Groups(['write:component-preparation', 'read:component-preparation'])
     ]
    private Employee $operator;

     #[
         ApiProperty(description: 'Etat de validation de la demande de préparation'),
         Column(options: ['default' => false], type: 'boolean'),
         Groups(['write:component-preparation', 'read:component-preparation'])
     ]
    private bool $valide = false;

     #[
         ApiProperty(description: 'Entrepôt de destination', readableLink: false, example: '/api/warehouses/1'),
         ManyToOne(targetEntity: Warehouse::class),
         Groups(['write:component-preparation', 'read:component-preparation'])
     ]
    private Warehouse $toWarehouse;

    #[
        ApiProperty(description: 'Entrepôt d\'origine du stock', readableLink: false, example: '/api/warehouses/1'),
        ManyToOne(targetEntity: Warehouse::class),
        Groups(['write:component-preparation', 'read:component-preparation'])
    ]
    private Warehouse $fromWarehouse;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:component-preparation'])
    ]
    private Blocker $embBlocker;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:component-preparation'])
    ]
    private State $embState;

    public function __construct() {
        $this->sentQuantity = new Measure();
        $this->requestedQuantity = new Measure();
        $this->embBlocker = new Blocker();
        $this->embState = new State();
    }

    public function getAskedBy(): Employee
    {
        return $this->askedBy;
    }

    public function setAskedBy(Employee $askedBy): void
    {
        $this->askedBy = $askedBy;
    }

    public function getComponent(): Component
    {
        return $this->component;
    }

    public function setComponent(Component $component): void
    {
        $this->component = $component;
    }

    public function getRequestDate(): \DateTimeImmutable
    {
        return $this->requestDate;
    }

    public function setRequestDate(\DateTimeImmutable $requestDate): void
    {
        $this->requestDate = $requestDate;
    }

    public function getRealizationDate(): \DateTimeInterface
    {
        return $this->realizationDate;
    }

    public function setRealizationDate(\DateTimeInterface $realizationDate): void
    {
        $this->realizationDate = $realizationDate;
    }

    public function getTargetLocation(): string
    {
        return $this->targetLocation;
    }

    public function setTargetLocation(string $targetLocation): void
    {
        $this->targetLocation = $targetLocation;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function getRequestedQuantity(): Measure
    {
        return $this->requestedQuantity;
    }

    public function setRequestedQuantity(Measure $requestedQuantity): void
    {
        $this->requestedQuantity = $requestedQuantity;
    }

    public function getSentQuantity(): Measure
    {
        return $this->sentQuantity;
    }

    public function setSentQuantity(Measure $sentQuantity): void
    {
        $this->sentQuantity = $sentQuantity;
    }

    public function getOperator(): Employee
    {
        return $this->operator;
    }

    final public function getEmbBlocker(): Blocker {
        return $this->embBlocker;
    }

    final public function getEmbState(): State {
        return $this->embState;
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    public function setOperator(Employee $operator): void
    {
        $this->operator = $operator;
    }

    public function isValide(): bool
    {
        return $this->valide;
    }

    public function setValide(bool $valide): void
    {
        $this->valide = $valide;
    }

    public function getToWarehouse(): Warehouse
    {
        return $this->toWarehouse;
    }

    public function setToWarehouse(Warehouse $toWarehouse): void
    {
        $this->toWarehouse = $toWarehouse;
    }

    public function getFromWarehouse(): Warehouse
    {
        return $this->fromWarehouse;
    }

    public function setFromWarehouse(Warehouse $fromWarehouse): void
    {
        $this->fromWarehouse = $fromWarehouse;
    }

        /**
     * @return $this
     */
    final public function setEmbBlocker(Blocker $embBlocker): self {
        $this->embBlocker = $embBlocker;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setEmbState(State $embState): self {
        $this->embState = $embState;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setBlocker(string $state): self {
        $this->embBlocker->setState($state);
        return $this;
    }
    /**
     * @return $this
     */
    final public function setState(string $state): self {
        $this->embState->setState($state);
        return $this;
    }
}