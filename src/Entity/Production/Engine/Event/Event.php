<?php

namespace App\Entity\Production\Engine\Event;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Production\Engine\EventType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Event as AbstractEvent;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Maintenance\Engine\Event\Maintenance;
use App\Entity\Maintenance\Engine\Event\Request;
use App\Entity\Production\Engine\Engine;
use Doctrine\ORM\Mapping as ORM;
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
            'groups' => ['read:event', 'read:id'],
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

    final public function getEmployee(): ?Employee {
        return $this->employee;
    }

    final public function getEngine(): ?Engine {
        return $this->engine;
    }

    final public function setEmployee(?Employee $employee): self {
        $this->employee = $employee;
        return $this;
    }

    final public function setEngine(?Engine $engine): self {
        $this->engine = $engine;
        return $this;
    }
}
