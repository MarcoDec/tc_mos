<?php

namespace App\Entity\Production\Engine\Workstation;

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
        description: 'Poste de travail',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les groupes de poste de travail',
                    'summary' => 'Récupère les groupes de poste de travail',
                    'tags' => ['EngineGroup']
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un groupe de poste de travail',
                    'summary' => 'Créer un groupe de poste de travail',
                    'tags' => ['EngineGroup']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ],
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        shortName: 'WorkstationGroup',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:engine-group'],
            'openapi_definition_name' => 'WorkstationGroup-write'
        ],
        normalizationContext: [
            'groups' => ['read:engine-group', 'read:id'],
            'openapi_definition_name' => 'WorkstationGroup-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
]
class Group extends EngineGroup {
    final public function getType(): string {
        return 'workstation';
    }
}
