<?php

namespace App\Entity\Hr\Event;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\ApiPlatform\Core\Annotation\ApiProperty;
use App\ApiPlatform\Core\Annotation\ApiSerializerGroups;
use App\Doctrine\DBAL\Types\Hr\Employee\CurrentPlaceType;
use App\Entity\Entity;
use App\Filter\EnumFilter;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Symfony\Component\Validator\Constraints\Choice;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: EnumFilter::class, properties: ['toStatus']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les types d\'événement',
                    'summary' => 'Récupère les types d\'événement',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un type d\'événement',
                    'summary' => 'Créer un type d\'événement',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un type d\'événement',
                    'summary' => 'Supprime un type d\'événement',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un type d\'événement',
                    'summary' => 'Modifie un type d\'événement',
                ]
            ]
        ],
        shortName: 'EventType',
        denormalizationContext: [
            'groups' => ['EventType-write'],
            'openapi_definition_name' => 'EventType-write'
        ],
        normalizationContext: [
            'groups' => ['EventType-read'],
            'openapi_definition_name' => 'EventType-read'
        ]
    ),
    ApiSerializerGroups(inheritedRead: ['EventType-read' => ['EventType-write', 'Entity']], write: ['EventType-write']),
    ORM\Entity,
    ORM\Table(name: 'event_type'),
    UniqueEntity('name')
]
class Type extends Entity {
    #[
        ApiProperty(example: 'ABSENCE', format: 'name'),
        Assert\Length(min: 3, max: 30),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(groups: ['EventType-read', 'EventType-write'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(ref: 'EventTypeStatusEnum'),
        Choice(choices: CurrentPlaceType::TYPES, name: 'EventTypeStatusEnum'),
        ORM\Column(type: 'employee_current_place', nullable: true, options: ['charset' => 'ascii']),
        Serializer\Groups(groups: ['EventType-read', 'EventType-write'])
    ]
    private ?string $toStatus = null;

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getToStatus(): ?string {
        return $this->toStatus;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setToStatus(?string $toStatus): self {
        $this->toStatus = $toStatus;
        return $this;
    }
}
