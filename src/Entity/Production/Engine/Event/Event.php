<?php

namespace App\Entity\Production\Engine\Event;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Production\Engine\EventType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Maintenance\Engine\Event\CurrentPlace;
use App\Entity\Event as AbstractEvent;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Interfaces\WorkflowInterface;
use App\Entity\Maintenance\Engine\Event\Maintenance;
use App\Entity\Maintenance\Engine\Event\Request;
use App\Entity\Production\Engine\Engine;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Événement sur un équipement',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les événements',
                    'summary' => 'Récupère les événements',
                ]
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
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'transition',
                        'required' => true,
                        'schema' => [
                            'enum' => CurrentPlace::TRANSITIONS,
                            'type' => 'string'
                        ]
                    ]],
                    'requestBody' => null,
                    'summary' => 'Transite le l\'événement à son prochain statut de workflow'
                ],
                'path' => '/engine-events/{id}/promote/{transition}',
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
            'groups' => ['read:current-place', 'read:event', 'read:id'],
            'openapi_definition_name' => 'EngineEvent-read',
            'skip_null_values' => false
        ]
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'engine_event_type'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'engine_event')
]
abstract class Event extends AbstractEvent implements WorkflowInterface {
    public const TYPES = [EventType::TYPE_MAINTENANCE => Maintenance::class, EventType::TYPE_REQUEST => Request::class];

    #[
        ORM\Embedded,
        Serializer\Groups(['read:event'])
    ]
    private CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Employé', example: '/api/employees/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:event', 'write:event'])
    ]
    private ?Employee $employee;

    #[
        ApiProperty(description: 'Machine', readableLink: false, example: '/api/engines/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:event', 'write:event'])
    ]
    private ?Engine $engine;

    public function __construct() {
        $this->currentPlace = new CurrentPlace();
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getEmployee(): ?Employee {
        return $this->employee;
    }

    final public function getEngine(): ?Engine {
        return $this->engine;
    }

    #[Pure]
    final public function getState(): ?string {
        return $this->currentPlace->getName();
    }

    #[Pure]
    final public function isDeletable(): bool {
        return $this->currentPlace->isDeletable();
    }

    #[Pure]
    final public function isFrozen(): bool {
        return $this->currentPlace->isFrozen();
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
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

    final public function setState(?string $state): self {
        $this->currentPlace->setName($state);
        return $this;
    }
}
