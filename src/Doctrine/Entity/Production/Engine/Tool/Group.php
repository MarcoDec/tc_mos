<?php

namespace App\Doctrine\Entity\Production\Engine\Tool;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\Entity\Embeddable\Hr\Employee\Roles;
use App\Doctrine\Entity\Production\Engine\Group as EngineGroup;
use Doctrine\ORM\Mapping as ORM;
use const NO_ITEM_GET_OPERATION;

#[
    ApiResource(
        description: 'Groupe d\'outil',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un groupe d\'outil',
                    'summary' => 'Créer un groupe d\'outil',
                    'tags' => ['EngineGroup']
                ]
            ],
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        shortName: 'ToolGroup',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
        ],
    ),
    ORM\Entity,
]
class Group extends EngineGroup {
}
