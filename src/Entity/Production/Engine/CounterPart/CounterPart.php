<?php

namespace App\Entity\Production\Engine\CounterPart;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Production\Engine\Engine;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Contrepartie de test',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les contreparties de test',
                    'summary' => 'Récupère les contreparties de test'
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une contrepartie de test',
                    'summary' => 'Créer une contrepartie de test'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ],
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une contrepartie de test',
                    'summary' => 'Supprime une contrepartie de test',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une contrepartie de test',
                    'summary' => 'Récupère uns contrepartie de test',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une contrepartie de test',
                    'summary' => 'Modifie une contrepartie de test',
                ]
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:engine'],
            'openapi_definition_name' => 'CounterPart-write'
        ],
        normalizationContext: [
            'enable_max_depth' => true,
            'groups' => ['read:engine', 'read:id', 'read:state'],
            'openapi_definition_name' => 'CounterPart-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class CounterPart extends Engine {
    #[
        ApiProperty(description: 'Groupe', readableLink: true, example: '/api/counter-part-groups/1'),
        ORM\ManyToOne(targetEntity: Group::class),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    protected $group;
}
