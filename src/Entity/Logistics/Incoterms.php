<?php

namespace App\Entity\Logistics;

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
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:incoterms', 'write:name'],
            'openapi_definition_name' => 'Incoterms-write'
        ],
        normalizationContext: [
            'groups' => ['read:incoterms', 'read:id', 'read:name'],
            'openapi_definition_name' => 'Incoterms-read'
        ],
    ),
    ORM\Entity,
    ORM\Table(name: 'incoterms')
]
class Incoterms extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Delivered Duty Paid'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Code ', required: true, example: 'DDP'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:incoterms', 'write:incoterms'])
    ]
    private ?string $code = null;

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }
}
