<?php

namespace App\Doctrine\Entity\Hr\Event;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Doctrine\DBAL\Types\Hr\Employee\CurrentPlaceType;
use App\Doctrine\Entity\Embeddable\Hr\Employee\Roles;
use App\Doctrine\Entity\Entity;
use App\Doctrine\Entity\Traits\NameTrait;
use App\Filter\EnumFilter;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use const NO_ITEM_GET_OPERATION;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: EnumFilter::class, properties: ['toStatus']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        description: 'Type d\'événements',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les types d\'événements',
                    'summary' => 'Récupère les types d\'événements',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un type d\'événements',
                    'summary' => 'Créer un type d\'événements',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un type d\'événements',
                    'summary' => 'Supprime un type d\'événements',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un type d\'événements',
                    'summary' => 'Modifie un type d\'événements',
                ]
            ]
        ],
        shortName: 'EventType',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name', 'write:type'],
            'openapi_definition_name' => 'EventType-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:type'],
            'openapi_definition_name' => 'EventType-read'
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'event_type'),
    UniqueEntity('name')
]
class Type extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'ABSENCE'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Status', example: 'blocked', openapiContext: ['enum' => CurrentPlaceType::TYPES]),
        Assert\Choice(choices: CurrentPlaceType::TYPES),
        ORM\Column(type: 'employee_current_place', nullable: true),
        Serializer\Groups(['read:type', 'write:type'])
    ]
    private ?string $toStatus = null;

    final public function getToStatus(): ?string {
        return $this->toStatus;
    }

    final public function setToStatus(?string $toStatus): self {
        $this->toStatus = $toStatus;
        return $this;
    }
}
