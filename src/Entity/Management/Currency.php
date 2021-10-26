<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['active']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['rate' => 'partial', 'code' => 'partial']),
    ApiResource(
        description: 'Currency',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les devises',
                    'summary' => 'Récupère les devises',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une devise',
                    'summary' => 'Créer une devise',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une devise',
                    'summary' => 'Supprime une devise',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une devise',
                    'summary' => 'Modifie une devise',
                ]
            ]
        ],
        shortName: 'Currency',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:currency', 'write:currency'],
            'openapi_definition_name' => 'Currency-write'
        ],
        normalizationContext: [
            'groups' => ['read:currency', 'read:id'],
            'openapi_definition_name' => 'Currency-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(),
    ORM\Table(name: 'incoterms')
]

class Currency extends Entity {
    #[
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:currency', 'write:currency'])
    ]
    private bool $active = false;

    #[
        ApiProperty(description: 'Code', required: true, example: 'EUR'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:currency', 'write:currency'])

    ]
    private ?string $code = null;

    #[
        ORM\Column(nullable: true),
        Serializer\Groups(['read:currency', 'write:currency'])

    ]
    private ?float $rate = null;

    public function getCode(): ?string {
        return $this->code;
    }

    public function getRate(): ?float {
        return $this->rate;
    }

    public function isActive(): bool {
        return $this->active;
    }

    public function setActive(bool $active): void {
        $this->active = $active;
    }

    public function setCode(?string $code): void {
        $this->code = $code;
    }

    public function setRate(?float $rate): void {
        $this->rate = $rate;
    }
}
