<?php

namespace App\Entity\Logistics;

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

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['code' => 'partial', 'name' => 'partial']),
    ApiResource(
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les incoterms',
                    'summary' => 'Récupère les incoterms',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un incoterms',
                    'summary' => 'Créer un incoterms',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un incoterms',
                    'summary' => 'Supprime un incoterms',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un incoterms',
                    'summary' => 'Modifie un incoterms',
                ]
            ]
        ],
        denormalizationContext: [
            'groups' => ['Incoterms-write'],
            'openapi_definition_name' => 'Incoterms-write'
        ],
        normalizationContext: [
            'groups' => ['Incoterms-read'],
            'openapi_definition_name' => 'Incoterms-read'
        ]
    ),
    ApiSerializerGroups(inheritedRead: ['Incoterms-read' => ['Incoterms-write', 'Entity']], write: ['Incoterms-write']),
    ORM\Entity,
    UniqueEntity('code'),
    UniqueEntity('name')
]
class Incoterms extends Entity {
    #[
        ApiProperty(example: 'DDP', format: 'codeValue'),
        Assert\Length(min: 3, max: 11),
        Assert\NotBlank,
        ORM\Column(length: 11),
        Serializer\Groups(groups: ['Incoterms-read', 'Incoterms-write'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(example: 'Delivered Duty Paid', format: 'name'),
        Assert\Length(min: 3, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50),
        Serializer\Groups(groups: ['Incoterms-read', 'Incoterms-write'])
    ]
    private ?string $name = null;

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
