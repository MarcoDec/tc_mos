<?php

namespace App\Entity\Production\Engine\Workstation;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Production\Engine\Engine;
use Doctrine\ORM\Mapping as ORM;

#[
    ApiResource(
        description: 'Poste de travail',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un poste de travail',
                    'summary' => 'Créer un poste de travail',
                    'tags' => ['Engine']
                ]
            ],
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
        ],
    ),
    ORM\Entity,
]
class Workstation extends Engine {
    #[
        ApiProperty(description: 'Group d\'outil', required: false, readableLink: false, example: '/api/workstation-groups/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Group::class),
    ]
    protected $group;
}
