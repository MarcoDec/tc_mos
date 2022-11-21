<?php

declare(strict_types=1);

namespace App\Entity\Hr\Employee\Event;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\Type\Hr\Employee\Event\EnumEmployeeEventType;
use App\Doctrine\Type\Hr\Employee\Role;
use App\Entity\Entity;
use App\Filter\SearchFilter;
use App\State\PersistProcessor;
use App\State\RemoveProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'toStatus' => 'exact']),
    ApiResource(
        shortName: 'EmployeeEventType',
        description: 'Type d\'événements sur les employés',
        operations: [
            new GetCollection(openapiContext: [
                'description' => 'Récupère les types d\'événements sur les employés',
                'summary' => 'Récupère les types d\'événements sur les employés'
            ]),
            new Post(
                openapiContext: [
                    'description' => 'Créer un type d\'événements sur les employés',
                    'summary' => 'Créer un type d\'événements sur les employés'
                ],
                processor: PersistProcessor::class
            ),
            new Delete(
                openapiContext: [
                    'description' => 'Supprime un type d\'événements sur les employés',
                    'summary' => 'Supprime un type d\'événements sur les employés'
                ],
                processor: RemoveProcessor::class
            ),
            new Patch(
                inputFormats: ['json' => ['application/merge-patch+json']],
                openapiContext: [
                    'description' => 'Modifie un type d\'événements sur les employés',
                    'summary' => 'Modifie un type d\'événements sur les employés'
                ],
                processor: PersistProcessor::class
            )
        ],
        inputFormats: 'json',
        outputFormats: 'jsonld',
        normalizationContext: [
            'groups' => ['id', 'employee-event-type-read'],
            'skip_null_values' => false,
            'openapi_definition_name' => 'employee-event-type-read'
        ],
        denormalizationContext: ['groups' => ['employee-event-type-write']],
        order: ['name' => 'asc'],
        security: Role::GRANTED_HR_ADMIN
    ),
    ORM\Entity,
    ORM\Table(name: 'employee_event_type'),
    UniqueEntity(fields: ['name', 'deleted'], ignoreNull: false)
]
class Type extends Entity {
    #[
        ApiProperty(description: 'Nom', example: 'Absence'),
        Assert\Length(min: 3, max: 30),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(['employee-event-type-read', 'employee-event-type-write'])
    ]
    private ?string $name = null;

    #[ORM\Column(type: 'employee_event', nullable: true)]
    private ?EnumEmployeeEventType $toStatus = null;

    public function getName(): ?string {
        return $this->name;
    }

    #[
        ApiProperty(
            description: 'Vers le statut',
            required: true,
            example: 'warning',
            openapiContext: ['enum' => EnumEmployeeEventType::ENUM]
        ),
        Serializer\Groups('employee-event-type-read')
    ]
    public function getToStatus(): ?EnumEmployeeEventType {
        return $this->toStatus;
    }

    public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    #[
        ApiProperty(
            description: 'Vers le statut',
            required: false,
            example: 'warning',
            openapiContext: ['enum' => EnumEmployeeEventType::ENUM]
        ),
        Serializer\Groups('employee-event-type-write')
    ]
    public function setToStatus(?EnumEmployeeEventType $toStatus): self {
        $this->toStatus = $toStatus;
        return $this;
    }
}
