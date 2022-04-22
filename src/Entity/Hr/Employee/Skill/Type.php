<?php

namespace App\Entity\Hr\Employee\Skill;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
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
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime les types de compétence',
                    'summary' => 'Supprime les types de compétence',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie les types de compétence',
                    'summary' => 'Modifie les types de compétence',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
            ]
        ],
        shortName: 'SkillType',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name'],
            'openapi_definition_name' => 'SkillType-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name'],
            'openapi_definition_name' => 'SkillType-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'skill_type'),
    UniqueEntity('name')
]
class Type extends Entity {
    #[
        ApiProperty(description: 'Nom', required: true, example: 'Assemblage'),
        Assert\Length(min: 3, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    private ?string $name = null;

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
