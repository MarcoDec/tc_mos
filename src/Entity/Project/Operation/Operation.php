<?php

namespace App\Entity\Project\Operation;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Traits\CodeTrait;
use App\Entity\Traits\NameTrait;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Opération',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les opérations',
                    'summary' => 'Récupère les opérations',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Crée une opération',
                    'summary' => 'Crée une opération',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une opération',
                    'summary' => 'Supprime une opération',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une opération',
                    'summary' => 'Récupère une opération',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une opération',
                    'summary' => 'Modifie une opération',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')'
            ],
        ],
        shortName: 'ProjectOperation',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PROJECT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name', 'write:code', 'write:family', 'write:project_operation'],
            'openapi_definition_name' => 'ProjectOperation-write'
        ],
        normalizationContext: [
            'groups' => ['read:name', 'read:id', 'read:code', 'read:family', 'read:project_operation'],
            'openapi_definition_name' => 'ProjectOperation-read'
        ]
    ),
    ORM\Entity,
    UniqueEntity(['name', 'code'])
]
class Operation extends Entity {
    use CodeTrait;
    use NameTrait;

    #[
        ApiProperty(description: 'Code', required: true, example: 'SAZ'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:code', 'write:code'])
    ]
    protected ?string $code = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Nom'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Automatique', required: false, example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:project_operation', 'write:project_operation'])
    ]
    private bool $auto = false;

    #[
        ApiProperty(description: 'Limite', required: false, example: 'Lorem ipsum'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:project_operation', 'write:project_operation'])
    ]
    private ?string $boundary = null;

    #[
        ApiProperty(description: 'Cadence', required: true, example: 0),
        ORM\Column(type: 'integer', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:project_operation', 'write:project_operation'])
    ]
    private int $cadence = 0;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      
    #[
        ApiProperty(description: 'Prix', required: true, example: 0),
        ORM\Column(type: 'float', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:project_operation', 'write:project_operation'])
    ]
    private float $price = 0;

    #[
        ApiProperty(description: 'Durée', required: true, example: 0),
        ORM\Column(type: 'integer', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:project_operation', 'write:project_operation'])
    ]
    private int $time = 0;

    #[
        ApiProperty(description: 'Type', required: false, example: '/api/operation-types/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Type::class),
        Serializer\Groups(['read:project_operation', 'write:project_operation'])
    ]
    private ?Type $type = null;

    final public function getBoundary(): ?string {
        return $this->boundary;
    }

    final public function getCadence(): int {
        return $this->cadence;
    }

    final public function getPrice(): float {
        return $this->price;
    }

    final public function getTime(): int {
        return $this->time;
    }

    final public function getType(): ?Type {
        return $this->type;
    }

    final public function isAuto(): bool {
        return $this->auto;
    }

    final public function setAuto(bool $auto): self {
        $this->auto = $auto;
        return $this;
    }

    final public function setBoundary(?string $boundary): self {
        $this->boundary = $boundary;
        return $this;
    }

    final public function setCadence(int $cadence): self {
        $this->cadence = $cadence;
        return $this;
    }

    final public function setPrice(float $price): self {
        $this->price = $price;
        return $this;
    }

    final public function setTime(int $time): self {
        $this->time = $time;
        return $this;
    }

    final public function setType(?Type $type): self {
        $this->type = $type;
        return $this;
    }
}
