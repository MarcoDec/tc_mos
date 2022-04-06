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
        description: 'Type qualité',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les types qualités',
                    'summary' => 'Récupère les types qualités',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un type qualité',
                    'summary' => 'Créer un type qualité',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un type qualité',
                    'summary' => 'Supprime un type qualité',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un type qualité',
                    'summary' => 'Modifie un type qualité',
                ]
            ]
        ],
        shortName: 'QualityType',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_QUALITY_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name'],
            'openapi_definition_name' => 'QualityType-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name'],
            'openapi_definition_name' => 'QualityType-read'
        ],
    ),
    ORM\Entity,
    ORM\Table(name: 'quality_type')
]
class Type extends Entity {
    #[
        ApiProperty(description: 'Nom ', required: true, example: 'Dimensions'),
        Assert\Length(min: 3, max: 40),
        Assert\NotBlank,
        ORM\Column(length: 40),
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
