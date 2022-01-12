<?php

namespace App\Entity\Hr\Event;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Event as AbstractEvent;
use App\Entity\Hr\Employee\Employee;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'employee' => 'name',
    ]),
    ApiResource(
        description: 'Événement',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les événements',
                    'summary' => 'Récupère les événements',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un événement',
                    'summary' => 'Créer un événement',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un événement',
                    'summary' => 'Supprime un événement',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un événement',
                    'summary' => 'Modifie un événement',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:employee', 'write:name', 'write:type', 'write:event_date'],
            'openapi_definition_name' => 'Event-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:employee', 'read:type', 'read:event_date'],
            'openapi_definition_name' => 'Event-read'
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'employee_event')
]

class Event extends AbstractEvent {
    #[
        ApiProperty(description: 'Employé', required: false, example: '/api/employees/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Employee::class),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?Employee $employee;

    #[
        ApiProperty(description: 'Type', required: true, readableLink: false, example: '/api/event-types/16'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Type::class),
        Serializer\Groups(['read:type', 'write:type'])
    ]
    private ?Type $type = null;

    final public function getEmployee(): ?Employee {
        return $this->employee;
    }

    final public function getType(): ?Type {
        return $this->type;
    }

    final public function setEmployee(?Employee $employee): self {
        $this->employee = $employee;
        return $this;
    }

    final public function setType(?Type $type): self {
        $this->type = $type;
        return $this;
    }
}
