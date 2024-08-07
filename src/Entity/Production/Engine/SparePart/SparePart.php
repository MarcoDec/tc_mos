<?php

namespace App\Entity\Production\Engine\SparePart;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Production\Engine\Engine;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'SparePart',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les pièces de rechange',
                    'summary' => 'Récupère les pièces de rechange'
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une pièce de rechange',
                    'summary' => 'Créer une pièce de rechange'
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
            'openapi_definition_name' => 'SparePart-write'
        ],
        normalizationContext: [
            'enable_max_depth' => true,
            'groups' => ['read:engine', 'read:id', 'read:state'],
            'openapi_definition_name' => 'SparePart-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class SparePart extends Engine {
    #[
        ApiProperty(description: 'Groupe', readableLink: false, example: '/api/spare-parts/1'),
        ORM\ManyToOne(targetEntity: Group::class),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    protected $group;
}
