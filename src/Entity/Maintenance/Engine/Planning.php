<?php

namespace App\Entity\Maintenance\Engine;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Production\Engine\Engine;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Planning de maintenance',
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
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un planning',
                    'summary' => 'Modifie un planning',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:planning'],
            'openapi_definition_name' => 'Planning-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:planning'],
            'openapi_definition_name' => 'Planning-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class Planning extends Entity {
    #[
        ApiProperty(description: 'Equivalent', readableLink: false, example: '/api/engines/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:planning', 'write:planning'])
    ]
    private ?Engine $engine = null;

    #[
        ApiProperty(description: 'Nom'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:planning', 'write:planning'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Quantité', openapiContext: ['$ref' => '#/components/schemas/Measure-duration']),
        ORM\Embedded,
        Serializer\Groups(['read:planning', 'write:planning'])
    ]
    private Measure $quantity;

    public function __construct() {
        $this->quantity = new Measure();
    }

    final public function getEngine(): ?Engine {
        return $this->engine;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getQuantity(): Measure {
        return $this->quantity;
    }

    final public function setEngine(?Engine $engine): self {
        $this->engine = $engine;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setQuantity(Measure $quantity): self {
        $this->quantity = $quantity;
        return $this;
    }
}
