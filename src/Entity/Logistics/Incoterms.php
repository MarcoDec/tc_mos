<?php

namespace App\Entity\Logistics;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['code', 'name']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'code' => 'partial']),
    ApiResource(
        description: 'Incoterms',
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
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un incoterms',
                    'summary' => 'Supprime un incoterms',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un incoterms',
                    'summary' => 'Modifie un incoterms',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:incoterms', 'write:name'],
            'openapi_definition_name' => 'Incoterms-write'
        ],
        normalizationContext: [
            'groups' => ['read:incoterms', 'read:id', 'read:name'],
            'openapi_definition_name' => 'Incoterms-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity,
    UniqueEntity('code'),
    UniqueEntity('name')
]
class Incoterms extends Entity {
    #[
        ApiProperty(description: 'Code ', required: true, example: 'DDP'),
        Assert\Length(min: 3, max: 11),
        Assert\NotBlank,
        ORM\Column(length: 11),
        Serializer\Groups(['read:incoterms', 'write:incoterms'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Delivered Duty Paid'),
        Assert\Length(min: 3, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50),
        Serializer\Groups(['read:name', 'write:name'])
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
