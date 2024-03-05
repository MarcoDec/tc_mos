<?php

namespace App\Entity\Production\Engine\Machine;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Production\Engine\Engine;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Machines',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les machines',
                    'summary' => 'Récupère les machines'
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une machine',
                    'summary' => 'Créer une machine'
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
            'openapi_definition_name' => 'Machine-write'
        ],
        normalizationContext: [
            'enable_max_depth' => true,
            'groups' => ['read:engine', 'read:id', 'read:state'],
            'openapi_definition_name' => 'Machine-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class Machine extends Engine {
    #[
        ApiProperty(description: 'Groupe', readableLink: false, example: '/api/machines/1'),
        ORM\ManyToOne(targetEntity: Group::class),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    protected $group;
}
