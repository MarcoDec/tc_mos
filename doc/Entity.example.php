<?php

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\ApiPlatform\Core\Annotation\ApiProperty;
use App\ApiPlatform\Core\Annotation\ApiSerializerGroups;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * Description.
 */
#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['property' => 'partial']),
    ApiResource(
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les entités',
                    'summary' => 'Récupère les entités',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une entité',
                    'summary' => 'Créer une entité',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une entité',
                    'summary' => 'Supprime une entité',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une entité',
                    'summary' => 'Modifie une entité',
                ]
            ]
        ],
        denormalizationContext: [
            'groups' => ['MyEntity-write'],
            'openapi_definition_name' => 'MyEntity-write'
        ],
        normalizationContext: [
            'groups' => ['MyEntity-read'],
            'openapi_definition_name' => 'MyEntity-read'
        ]
    ),
    ApiSerializerGroups(inheritedRead: ['MyEntity-read' => ['Entity', 'MyEntity-write']], write: ['MyEntity-write']),
    ORM\Entity
]
class MyEntity extends Entity {
    #[
        ApiProperty,
        ORM\Column,
        Serializer\Groups(groups: ['MyEntity-read', 'MyEntity-write'])
    ]
    private mixed $property;
}
