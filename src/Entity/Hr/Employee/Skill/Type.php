<?php

namespace App\Entity\Hr\Employee\Skill;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [ 'name' => 'partial']),
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
        shortName: 'TypeSkills',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:type', 'write:name'],
            'openapi_definition_name' => 'TypeSkill-write'
        ],
        normalizationContext: [
            'groups' => ['read:type', 'read:id', 'read:name'],
            'openapi_definition_name' => 'TypeSkill-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(),
    ORM\Table(name: 'skill_type')
]
class Type extends Entity {

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Faisceaux'),
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name']),
        Assert\NotBlank
        ]
    private ?string $name;


    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }
}
