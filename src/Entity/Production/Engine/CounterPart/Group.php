<?php

namespace App\Entity\Production\Engine\CounterPart;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Production\Engine\Group as EngineGroup;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['safetyDevice']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'code' => 'partial']),
    ApiResource(
        description: 'EngineGroup',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'créer un groupe CounterPart',
                    'summary' => 'Récupère un groupe CounterPart',
                ]
            ],
        ],
        shortName: 'Group',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:group', 'write:group'],
            'openapi_definition_name' => 'Group-write'
        ],
        normalizationContext: [
            'groups' => ['read:group', 'read:id', 'read:name'],
            'openapi_definition_name' => 'Group-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(),

]
class Group extends EngineGroup {
    final public function getType(): string {
        return 'counter';
    }
}
