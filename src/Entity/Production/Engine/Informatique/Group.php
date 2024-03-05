<?php

namespace App\Entity\Production\Engine\Informatique;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Production\Engine\Group as EngineGroup;
use Doctrine\ORM\Mapping as ORM;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['safetyDevice']),
    ApiFilter(filterClass: OrderFilter::class, properties: ['code', 'name']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'code' => 'partial']),
    ApiResource(
        description: 'Groupe d\'équipement informatique',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les groupes d\'équipement informatique',
                    'summary' => 'Récupère les groupes d\'équipement informatique',
                    'tags' => ['EngineGroup']
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un groupe d\'équipement informatique',
                    'summary' => 'Créer un groupe d\'équipement informatique',
                    'tags' => ['EngineGroup']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ],
        ],
        itemOperations: ['get'],
        shortName: 'InformatiqueGroup',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:engine-group'],
            'openapi_definition_name' => 'InformatiqueGroup-write'
        ],
        normalizationContext: [
            'groups' => ['read:engine-group', 'read:id'],
            'openapi_definition_name' => 'InformatiqueGroup-read',
            'skip_null_values' => false
        ],
        paginationEnabled: false
    ),
    ORM\Entity,
]
class Group extends EngineGroup {
    final public function getType(): string {
        return 'informatique';
    }
}
