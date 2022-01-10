<?php

namespace App\Entity\Maintenance\Engine;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Production\Engine\Engine;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Planning',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les plannings',
                    'summary' => 'Récupère les plannings',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un planning',
                    'summary' => 'Créer un planning',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un planning',
                    'summary' => 'Supprime un planning',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un planning',
                    'summary' => 'Récupère un planning',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un planning',
                    'summary' => 'Modifie un planning',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_WRITER.'\')'
            ]
        ],
        shortName: 'EnginePlanning',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:planning', 'write:name', 'write:engine'],
            'openapi_definition_name' => 'EnginePlanning-write'
        ],
        normalizationContext: [
            'groups' => ['read:planning', 'read:id', 'read:name', 'read:engine'],
            'openapi_definition_name' => 'EnginePlanning-read'
        ]
    ),
    ORM\Entity
]
class Planning extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Nom', required: true),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Equivalent', readableLink: false, example: '/api/manufacturer-engines/1'),
        ORM\ManyToOne(targetEntity: Engine::class, inversedBy: 'children'),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ?Engine $engine = null;

    #[
        ApiProperty(description: 'Type', required: false, example: 'Ipsum'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:planning', 'write:planning'])
    ]
    private ?string $kind = null;

    #[
        ApiProperty(description: 'Quantité', required: true, example: 0),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:planning', 'write:planning'])
    ]
    private int $quantity = 0;

    final public function getEngine(): ?Engine {
        return $this->engine;
    }

    final public function getKind(): ?string {
        return $this->kind;
    }

    final public function getQuantity(): int {
        return $this->quantity;
    }

    final public function setEngine(?Engine $engine): self {
        $this->engine = $engine;
        return $this;
    }

    final public function setKind(?string $kind): self {
        $this->kind = $kind;
        return $this;
    }

    final public function setQuantity(int $quantity): self {
        $this->quantity = $quantity;
        return $this;
    }
}
