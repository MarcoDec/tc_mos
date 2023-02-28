<?php

namespace App\Entity\Production\Engine\Workstation;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Production\Engine\Engine;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Poste de travail',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les postes de travail',
                    'summary' => 'Récupère les postes de travail',
                    'tags' => ['Engine']
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un poste de travail',
                    'summary' => 'Créer un poste de travail',
                    'tags' => ['Engine']
                ]
            ]
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:engine'],
            'openapi_definition_name' => 'Workstation-write'
        ],
        normalizationContext: [
            'enable_max_depth' => true,
            'groups' => ['read:engine', 'read:id', 'read:state'],
            'openapi_definition_name' => 'Workstation-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class Workstation extends Engine {
    #[
        ApiProperty(description: 'Groupe', readableLink: false, example: '/api/workstation-groups/1'),
        ORM\ManyToOne(targetEntity: Group::class),
        Serializer\Groups(['read:engine', 'write:engine','read:manufacturing-operation'])
    ]
    protected $group;
}
