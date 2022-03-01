<?php

namespace App\Doctrine\Entity\Production\Engine\Workstation;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\Entity\Embeddable\Hr\Employee\Roles;
use App\Doctrine\Entity\Production\Engine\Group as EngineGroup;
use Doctrine\ORM\Mapping as ORM;
use const NO_ITEM_GET_OPERATION;

#[
    ApiResource(
        description: 'WorkstationGroup',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un groupe de poste de travail',
                    'summary' => 'Créer un groupe de poste de travail',
                    'tags' => ['EngineGroup']
                ]
            ],
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        shortName: 'WorkstationGroup',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
        ],
    ),
    ORM\Entity,
]
class Group extends EngineGroup {
}
