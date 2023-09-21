<?php

namespace App\Entity\Hr\Event;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Event as AbstractEvent;
use App\Entity\Hr\Employee\Employee;
use App\Filter\RelationFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['employee', 'type']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['date' => 'partial', 'name' => 'partial',]),
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
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un événement',
                    'summary' => 'Modifie un événement',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')'
            ]
        ],
        shortName: 'EmployeeEvent',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:event'],
            'openapi_definition_name' => 'EmployeeEvent-write'
        ],
        normalizationContext: [
            'groups' => ['read:event', 'read:id'],
            'openapi_definition_name' => 'EmployeeEvent-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'employee_event')
]
class Event extends AbstractEvent {
    #[
        ApiProperty(description: 'Employé',readableLink: false ,example: '/api/employees/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:event', 'write:event'])
    ]
    private ?Employee $employee = null;

    #[
        ApiProperty(description: 'Type', readableLink: false, example: '/api/event-types/16'),
        ORM\ManyToOne,
        Serializer\Groups(['read:event', 'write:event'])
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
