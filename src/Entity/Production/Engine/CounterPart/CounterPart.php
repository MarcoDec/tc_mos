<?php

namespace App\Entity\Production\Engine\CounterPart;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Production\Engine\Engine;
use Doctrine\ORM\Mapping as ORM;

#[
    ApiResource(
        description: 'Contrepartie de test',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les contreparties de test',
                    'summary' => 'Récupère les contreparties de test',
                    'tags' => ['Engine']
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une contrepartie de test',
                    'summary' => 'Créer une contrepartie de test',
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
    ORM\Entity
]
class CounterPart extends Engine {
    #[
        ApiProperty(description: 'Groupe', readableLink: true, example: '/api/counter-part-groups/1'),
        ORM\ManyToOne(targetEntity: Group::class)
    ]
    protected $group;
}
