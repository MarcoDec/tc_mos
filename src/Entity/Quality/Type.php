<?php

namespace App\Entity\Quality;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        description: 'Quality',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les qualités',
                    'summary' => 'Récupère les qualités',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une qualité',
                    'summary' => 'Créer une qualité',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une qualité',
                    'summary' => 'Supprime une qualité',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une qualité',
                    'summary' => 'Modifie une qualité',
                ]
            ]
        ],
        shortName: 'Qualite',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_QUALITY_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:qualite', 'write:name'],
            'openapi_definition_name' => 'Qualite-write'
        ],
        normalizationContext: [
            'groups' => ['read:qualite', 'read:id', 'read:name'],
            'openapi_definition_name' => 'Qualite-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(),
    ORM\Table(name: 'quality_type')
]
class Type extends Entity {
    #[
        ApiProperty(description: 'Nom ', required: true, example: 'Dimensions'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name;

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }
}
