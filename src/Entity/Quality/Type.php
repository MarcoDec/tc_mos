<?php

namespace App\Entity\Quality;

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
 * Type qualité.
 */
#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
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
        denormalizationContext: [
            'groups' => ['QualityType-write'],
            'openapi_definition_name' => 'QualityType-write'
        ],
        normalizationContext: [
            'groups' => ['QualityType-read'],
            'openapi_definition_name' => 'QualityType-read'
        ]
    ),
    ApiSerializerGroups(inheritedRead: ['QualityType-read' => ['QualityType-write', 'Entity']], write: ['QualityType-write']),
    ORM\Entity,
    ORM\Table(name: 'quality_type'),
    UniqueEntity('name')
]
class Type extends Entity {
    /**
     * @var null|string Nom
     */
    #[
        ApiProperty(example: 'Dimensions', format: 'name'),
        Assert\Length(min: 3, max: 20),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(groups: ['QualityType-read', 'QualityType-write'])
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
