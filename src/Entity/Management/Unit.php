<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Filter\RelationFilter;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['parent']),
    ApiFilter(filterClass: OrderFilter::class, properties: ['base', 'code', 'name', 'parent.code']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'code' => 'partial']),
    ApiResource(
        description: 'Unit',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les unités',
                    'summary' => 'Récupère les unités',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une unité',
                    'summary' => 'Créer une unité',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une unité',
                    'summary' => 'Supprime une unité',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une unité',
                    'summary' => 'Modifie une unité',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name', 'write:unit'],
            'openapi_definition_name' => 'Unit-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:unit'],
            'openapi_definition_name' => 'Unit-read'
        ]
    ),
    ORM\Entity,
    UniqueEntity('code'),
    UniqueEntity('name')
]
class Unit extends AbstractUnit {
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    protected Collection $children;

    #[
        ApiProperty(description: 'Parent ', readableLink: false, example: '/api/units/1'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    protected $parent;
}
