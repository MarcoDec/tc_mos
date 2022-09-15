<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Filter\NumericFilter;
use App\Filter\RelationFilter;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Validator\Management\Unit\Base;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: NumericFilter::class, properties: ['base']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['parent']),
    ApiFilter(filterClass: OrderFilter::class, properties: ['base', 'code', 'name', 'parent.code']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['code' => 'partial', 'name' => 'partial']),
    ApiResource(
        description: 'Unité',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les unités',
                    'summary' => 'Récupère les unités'
                ]
            ],
            'options' => [
                'controller' => PlaceholderAction::class,
                'filters' => [],
                'method' => 'GET',
                'normalization_context' => [
                    'groups' => ['read:id', 'read:unit:option'],
                    'openapi_definition_name' => 'Unit-options',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les unités pour les select',
                    'summary' => 'Récupère les unités pour les select',
                ],
                'order' => ['code' => 'asc'],
                'pagination_enabled' => false,
                'path' => '/units/options',
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\') or is_granted(\''.Roles::ROLE_MANAGEMENT_READER.'\')'
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une unité',
                    'summary' => 'Créer une unité'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une unité',
                    'summary' => 'Supprime une unité'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une unité',
                    'summary' => 'Modifie une unité'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:unit'],
            'openapi_definition_name' => 'Unit-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:unit'],
            'openapi_definition_name' => 'Unit-read',
            'skip_null_values' => false
        ]
    ),
    Base,
    ORM\Entity,
    UniqueEntity('code'),
    UniqueEntity('name')
]
class Unit extends AbstractUnit {
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, fetch: 'EAGER')]
    protected Collection $children;

    #[
        ApiProperty(description: 'Parent ', readableLink: false, example: '/api/units/1'),
        ORM\ManyToOne(targetEntity: self::class, fetch: 'EAGER', inversedBy: 'children'),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    protected $parent;
}
