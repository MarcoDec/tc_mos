<?php

namespace App\Entity\Quality\Reject;

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
 * Types de rebus.
 */
#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les types de rebus',
                    'summary' => 'Récupère les types de rebus',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un type de rebus',
                    'summary' => 'Créer un type de rebus',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un type de rebus',
                    'summary' => 'Supprime un type de rebus',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un type de rebus',
                    'summary' => 'Modifie un type de rebus',
                ]
            ]
        ],
        shortName: 'RejectType',
        denormalizationContext: [
            'groups' => ['RejectType-write'],
            'openapi_definition_name' => 'RejectType-write'
        ],
        normalizationContext: [
            'groups' => ['RejectType-read'],
            'openapi_definition_name' => 'RejectType-read'
        ]
    ),
    ApiSerializerGroups(inheritedRead: ['RejectType-read' => ['RejectType-write', 'Entity']], write: ['RejectType-write']),
    ORM\Entity,
    ORM\Table(name: 'reject_type'),
    UniqueEntity('name')
]
class Type extends Entity {
    /**
     * @var null|string Nom
     */
    #[
        ApiProperty(example: 'sertissage dimensionnelle', format: 'name'),
        Assert\Length(min: 3, max: 20),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(groups: ['RejectType-read', 'RejectType-write'])
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
