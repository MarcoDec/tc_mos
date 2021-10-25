<?php

namespace App\Entity\Logistics;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

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
                    'description' => 'Créer un incoterm',
                    'summary' => 'Créer un incoterm',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un incoterm',
                    'summary' => 'Supprime un incoterm',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un incoterm',
                    'summary' => 'Modifie un incoterm',
                ]
            ]
        ],
        shortName: 'Incoterms',
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
        paginationEnabled: false
    ),
    ORM\Entity(),
    ORM\Table(name: 'incoterms')
]

class Incoterms extends Entity {
    #[
        ApiProperty(description: 'Code ', example: 'FCA'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:incoterms', 'write:incoterms'])

    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Nom ', example: 'Free Carrier'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name', 'write:name'])

    ]
    private ?string $name = null;

    public function getCode(): ?string {
        return $this->code;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setCode(?string $code): void {
        $this->code = $code;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }
}
