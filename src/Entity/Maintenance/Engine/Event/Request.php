<?php

namespace App\Entity\Maintenance\Engine\Event;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Production\Engine\Event\Event;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Requête',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les requêtes',
                    'summary' => 'Récupère les requêtes',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une requête',
                    'summary' => 'Créer une requête',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une requête',
                    'summary' => 'Supprime une requête',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_ADMIN.'\')'
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une requête',
                    'summary' => 'Modifie une requête',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_WRITER.'\')'
            ]
        ],
        shortName: 'EngineRequest',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:planning', 'write:name', 'write:event_date', 'write:notes', 'write:request'],
            'openapi_definition_name' => 'EngineRequest-write'
        ],
        normalizationContext: [
            'groups' => ['read:planning', 'read:id', 'read:name', 'read:event_date', 'read:event', 'read:company', 'read:employee', 'read:engine', 'read:notes', 'read:request'],
            'openapi_definition_name' => 'EngineRequest-read'
        ]
    ),
    ORM\Entity
]
class Request extends Event {
    #[
        ApiProperty(description: 'Urgence', example: 1),
        ORM\Column(type: 'tinyint', options: ['default' => 1, 'unsigned' => true]),
        Serializer\Groups(['read:request', 'write:request'])
    ]
    private int $emergency = 1;

    #[
        ApiProperty(description: 'Notes d\'intervention', example: 'Lorem ipsum dolores'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:request', 'write:request'])
    ]
    private ?string $interventionNotes = null;

    #[
        ApiProperty(description: 'Notes', example: 'Demande technique sur l\'outil'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:notes', 'write:notes'])
    ]
    private ?string $notes = null;

    final public function getEmergency(): int {
        return $this->emergency;
    }

    final public function getInterventionNotes(): ?string {
        return $this->interventionNotes;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getType(): string {
        return 'request';
    }

    final public function setEmergency(int $emergency): self {
        $this->emergency = $emergency;
        return $this;
    }

    final public function setInterventionNotes(?string $interventionNotes): self {
        $this->interventionNotes = $interventionNotes;
        return $this;
    }

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }
}
