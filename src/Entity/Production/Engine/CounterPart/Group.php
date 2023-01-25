<?php

namespace App\Entity\Production\Engine\CounterPart;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Production\Engine\Group as EngineGroup;
use Doctrine\ORM\Mapping as ORM;

#[
    ApiResource(
        description: 'Groupe de contrepartie de test',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'créer un groupe de contrepartie de test',
                    'summary' => 'Créer un groupe de contrepartie de test',
                    'tags' => ['EngineGroup']
                ]
            ],
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        shortName: 'CounterPartGroup',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
        ]
    ),
    ORM\Entity
]
class Group extends EngineGroup {
    final public function getType(): string {
        return 'counter-part';
    }
}
