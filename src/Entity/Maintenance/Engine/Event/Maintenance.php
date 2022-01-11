<?php

namespace App\Entity\Maintenance\Engine\Event;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Maintenance\Engine\Planning;
use App\Entity\Production\Engine\Event\Event;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Maintenance',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les maintenances',
                    'summary' => 'Récupère les maintenances',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une maintenance',
                    'summary' => 'Créer une maintenance',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une maintenance',
                    'summary' => 'Supprime une maintenance',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_ADMIN.'\')'
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une maintenance',
                    'summary' => 'Modifie une maintenance',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:planning', 'write:name', 'write:event_date'],
            'openapi_definition_name' => 'Maintenance-write'
        ],
        normalizationContext: [
            'groups' => ['read:planning', 'read:id', 'read:name', 'read:event_date', 'read:event', 'read:company', 'read:employee', 'read:engine'],
            'openapi_definition_name' => 'Maintenance-read'
        ]
    ),
    ORM\Entity
]
class Maintenance extends Event {
    #[
        ApiProperty(description: 'Planifié par', required: false, example: '/api/engine-plannings/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Planning::class),
        Serializer\Groups(['write:planning', 'read:planning'])
    ]
    protected ?Planning $plannedBy = null;

    final public function getPlannedBy(): ?Planning {
        return $this->plannedBy;
    }

    final public function getType(): string {
        return 'maintenance';
    }

    final public function setPlannedBy(?Planning $plannedBy): self {
        $this->plannedBy = $plannedBy;
        return $this;
    }
}
