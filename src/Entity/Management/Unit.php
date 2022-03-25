<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
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
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une unité',
                    'summary' => 'Supprime une unité',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une unité',
                    'summary' => 'Modifie une unité',
                ]
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
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
    #[
        ApiProperty(description: 'Enfants ', readableLink: false, example: ['/api/units/2', '/api/units/3']),
        ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class),
        Serializer\Groups(['read:unit'])
    ]
    protected Collection $children;

    #[
        ApiProperty(description: 'Parent ', readableLink: false, example: '/api/units/1'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    protected $parent;
}
