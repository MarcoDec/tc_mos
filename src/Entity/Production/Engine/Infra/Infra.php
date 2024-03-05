<?php

namespace App\Entity\Production\Engine\Infra;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Production\Engine\Engine;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Infra',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les éléments d\'infrastructure',
                    'summary' => 'Récupère les éléments d\'infrastructure'
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un élément d\'infrastructure',
                    'summary' => 'Créer un élément d\'infrastructure'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ],
        ],
        itemOperations: ['get', 'patch', 'delete'],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:engine'],
            'openapi_definition_name' => 'Infra-write'
        ],
        normalizationContext: [
            'enable_max_depth' => true,
            'groups' => ['read:engine', 'read:id', 'read:state'],
            'openapi_definition_name' => 'Infra-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class Infra extends Engine {
    #[
        ApiProperty(description: 'Groupe', readableLink: false, example: '/api/infras/1'),
        ORM\ManyToOne(targetEntity: Group::class),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    protected $group;
}
