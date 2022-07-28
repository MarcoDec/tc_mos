<?php

namespace App\Entity\Production\Engine\Tool;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Production\Engine\Engine;
use Doctrine\ORM\Mapping as ORM;

#[
    ApiResource(
        description: 'Outil',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les outils',
                    'summary' => 'Récupère les outils',
                    'tags' => ['Engine']
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un outil',
                    'summary' => 'Créer un outil',
                    'tags' => ['Engine']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ],
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
    ),
    ORM\Entity,
]
class Tool extends Engine {
    #[
        ApiProperty(description: 'Group d\'outil', readableLink: false, example: '/api/tool-groups/1'),
        ORM\ManyToOne(targetEntity: Group::class),
    ]
    protected $group;
}
