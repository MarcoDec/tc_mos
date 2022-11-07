<?php

declare(strict_types=1);

namespace App\Entity\Management;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Entity;
use App\State\PersistProcessor;
use App\State\RemoveProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Unité',
        operations: [
            new GetCollection(
                uriTemplate: '/units/options',
                openapiContext: [
                    'description' => 'Récupère les unités pour les select',
                    'summary' => 'Récupère les unités pour les select'
                ],
                paginationEnabled: false,
                normalizationContext: [
                    'groups' => ['id', 'unit-option'],
                    'skip_null_values' => false,
                    'openapi_definition_name' => 'unit-option'
                ]
            ),
            new GetCollection(
                openapiContext: ['description' => 'Récupère les unités', 'summary' => 'Récupère les unités'],
                filters: ['unit.numeric_filter', 'unit.order_filter', 'unit.search_filter']
            ),
            new Post(
                openapiContext: ['description' => 'Créer une unité', 'summary' => 'Créer une unité'],
                processor: PersistProcessor::class
            ),
            new Delete(
                openapiContext: ['description' => 'Supprime une unité', 'summary' => 'Supprime une unité'],
                processor: RemoveProcessor::class
            ),
            new Patch(
                inputFormats: ['json' => ['application/merge-patch+json']],
                openapiContext: ['description' => 'Modifie une unité', 'summary' => 'Modifie une unité'],
                processor: PersistProcessor::class
            )
        ],
        inputFormats: 'json',
        outputFormats: 'jsonld',
        normalizationContext: [
            'groups' => ['id', 'unit-read'],
            'skip_null_values' => false,
            'openapi_definition_name' => 'unit-read'
        ],
        denormalizationContext: ['groups' => ['unit-write']],
        order: ['code' => 'asc']
    ),
    ORM\Entity,
    UniqueEntity(fields: ['code', 'deleted'], ignoreNull: true),
    UniqueEntity(fields: ['name', 'deleted'], ignoreNull: true)
]
class Unit extends Entity {
    #[
        ApiProperty(description: 'Base', example: 1),
        Assert\NotBlank,
        Assert\Positive,
        ORM\Column(options: ['default' => 1]),
        Serializer\Groups(['unit-read', 'unit-write'])
    ]
    private float $base = 1;

    #[
        ApiProperty(description: 'Code', example: 'g'),
        Assert\Length(min: 1, max: 6),
        Assert\NotBlank,
        ORM\Column(length: 6, options: ['collation' => 'utf8mb3_bin']),
        Serializer\Groups(['unit-read', 'unit-write'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Nom', example: 'Gramme'),
        Assert\Length(min: 5, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50),
        Serializer\Groups(['unit-read', 'unit-write'])
    ]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $parent = null;

    public function getBase(): float {
        return $this->base;
    }

    public function getCode(): ?string {
        return $this->code;
    }

    public function getName(): ?string {
        return $this->name;
    }

    #[
        ApiProperty(description: 'Parent', readableLink: false, writableLink: false, required: true, example: '/api/units/1'),
        Serializer\Groups('unit-read')
    ]
    public function getParent(): ?self {
        return $this->parent;
    }

    #[ApiProperty(required: true), Serializer\Groups('unit-option')]
    public function getText(): ?string {
        return $this->code;
    }

    public function setBase(float $base): self {
        $this->base = $base;
        return $this;
    }

    public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    #[
        ApiProperty(description: 'Parent', readableLink: false, writableLink: false, required: false, example: '/api/units/1'),
        Serializer\Groups('unit-write')
    ]
    public function setParent(?self $parent): self {
        $this->parent = $parent;
        return $this;
    }
}
