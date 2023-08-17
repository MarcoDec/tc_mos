<?php

namespace App\Entity\Production\Engine\Event;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use App\Doctrine\DBAL\Types\Production\Engine\EventType;
use App\Entity\Embeddable\EventState;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Event as AbstractEvent;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Maintenance\Engine\Event\Maintenance;
use App\Entity\Maintenance\Engine\Event\Request;
use App\Entity\Production\Engine\Engine;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use App\Filter\DiscriminatorFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\Production\Engine\ItemEventEquipementEmployeeController;
use App\Controller\Production\Engine\ItemEventEquipementTypeController;


#[
    ApiFilter(DiscriminatorFilter::class),
    ApiFilter(filterClass: BooleanFilter::class, properties: ['done']),
    ApiFilter(filterClass: DateFilter::class, properties: ['date']),
    ApiResource(
        description: 'Événement sur un équipement',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les événements',
                    'summary' => 'Récupère les événements',
                ]
            ],
            'filtreEmployee' => [
                'controller' => ItemEventEquipementEmployeeController::class,
                'method' => 'GET',
                'openapi_context' => [
                    'description' => 'Filtrer par engine',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'api',
                        'schema' => [
                            'type' => 'integer',
                        ]
                    ]],
                    'summary' => 'Filtrer par engine'
                ],
                'path' => '/engine-events/filtreEmployee/{api}',
                'read' => false,
                'write' => false
            ],
            'filtreEvent' => [
                'controller' => ItemEventEquipementTypeController::class,
                'method' => 'GET',
                'openapi_context' => [
                    'description' => 'Filtrer par engine',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'api',
                        'schema' => [
                            'type' => 'integer',
                        ]
                    ]],
                    'summary' => 'Filtrer par engine'
                ],
                'path' => '/engine-events/filtreEvent/{api}',
                'read' => false,
                'write' => false
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un événement',
                    'summary' => 'Supprime un événement',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un événement',
                    'summary' => 'Modifie un événement',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_WRITER.'\')'
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite l\'événement à son prochain statut de workflow',
                    'parameters' => [
                        [
                            'in' => 'path',
                            'name' => 'transition',
                            'required' => true,
                            'schema' => ['enum' => EventState::TRANSITIONS, 'type' => 'string']
                        ],
                        [
                            'in' => 'path',
                            'name' => 'workflow',
                            'required' => true,
                            'schema' => ['enum' => ['event'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite le l\'événement à son prochain statut de workflow'
                ],
                'path' => '/engine-events/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_WRITER.'\')',
                'validate' => false
            ]
        ],
        shortName: 'EngineEvent',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:event'],
            'openapi_definition_name' => 'EngineEvent-write'
        ],
        normalizationContext: [
            'groups' => ['read:event', 'read:id', 'read:state'],
            'openapi_definition_name' => 'EngineEvent-read',
            'skip_null_values' => false
        ]
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'engine_event'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'engine_event')
]
abstract class Event extends AbstractEvent {
    final public const TYPES = [EventType::TYPE_MAINTENANCE => Maintenance::class, EventType::TYPE_REQUEST => Request::class];

    #[
        ORM\Embedded,
        Serializer\Groups(['read:event','read:engine-maintenance-event'])
    ]
    protected EventState $embState;

    #[
        ApiProperty(description: 'Employé'),
        ORM\ManyToOne,
        Serializer\Groups(['read:event', 'write:event','read:engine-maintenance-event','write:engine-maintenance-event'])
    ]
    protected ?Employee $employee;

    #[
        ApiProperty(description: 'Machine'),
        ORM\ManyToOne,
        Serializer\Groups(['read:event', 'write:event','read:engine-maintenance-event'])
    ]
    protected ?Engine $engine;

    #[Pure] public function __construct() {
        $this->name = "Evènement Machine";
        $this->embState = new EventState();
    }

    final public function getEmbState(): EventState {
        return $this->embState;
    }

    final public function getEmployee(): ?Employee {
        return $this->employee;
    }

    final public function getEngine(): ?Engine {
        return $this->engine;
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    final public function setEmbState(EventState $embState): self {
        $this->embState = $embState;
        return $this;
    }

    final public function setEmployee(?Employee $employee): self {
        $this->employee = $employee;
        return $this;
    }

    final public function setEngine(?Engine $engine): self {
        $this->engine = $engine;
        return $this;
    }

    final public function setState(string $state): self {
        $this->embState->setState($state);
        return $this;
    }
}
