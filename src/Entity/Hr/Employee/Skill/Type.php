<?php

namespace App\Entity\Hr\Employee\Skill;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\ApiPlatform\Core\Annotation\ApiProperty;
use App\ApiPlatform\Core\Annotation\ApiSerializerGroups;
use App\Entity\Entity;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Type de compétence.
 */
#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les types de compétence',
                    'summary' => 'Récupère les types de compétence',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un type de compétence',
                    'summary' => 'Créer un type de compétence',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un type de compétence',
                    'summary' => 'Supprime un type de compétence',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un type de compétence',
                    'summary' => 'Modifie un type de compétence',
                ]
            ]
        ],
        shortName: 'SkillType',
        denormalizationContext: [
            'groups' => ['SkillType-write'],
            'openapi_definition_name' => 'SkillType-write'
        ],
        normalizationContext: [
            'groups' => ['SkillType-read'],
            'openapi_definition_name' => 'SkillType-read'
        ]
    ),
    ApiSerializerGroups(inheritedRead: ['SkillType-read' => ['SkillType-write', 'Entity']], write: ['SkillType-write']),
    ORM\Entity,
    ORM\Table(name: 'skill_type'),
    UniqueEntity('name')
]
class Type extends Entity {
    /**
     * @var null|string Nom
     */
    #[
        ApiProperty(example: 'Assemblage', format: 'name'),
        Assert\Length(min: 3, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50),
        Serializer\Groups(groups: ['SkillType-read', 'SkillType-write'])
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
