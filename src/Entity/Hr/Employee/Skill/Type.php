<?php

namespace App\Entity\Hr\Employee\Skill;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        description: 'Type de compétence',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les types de compétence',
                    'summary' => 'Récupère les types de compétence',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer les types de compétence',
                    'summary' => 'Créer les types de compétence',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime les types de compétence',
                    'summary' => 'Supprime les types de compétence',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie les types de compétence',
                    'summary' => 'Modifie les types de compétence',
                ]
            ]
        ],
        shortName: 'SkillType',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name', 'write:type'],
            'openapi_definition_name' => 'SkillType-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:type'],
            'openapi_definition_name' => 'SkillType-read'
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'skill_type')
]
class Type extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Assemblage'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;
}
