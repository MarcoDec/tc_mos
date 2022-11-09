<?php

declare(strict_types=1);

namespace App\Entity\Management;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\Type\Hr\Employee\Role;
use App\Entity\Entity;
use App\Entity\Purchase\Component\Attribute;
use App\Repository\Management\UnitRepository;
use App\State\Management\UnitProvider;
use App\State\PersistProcessor;
use App\State\RemoveProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
            new Get(
                openapiContext: ['description' => 'Récupère une unité', 'summary' => 'Récupère une unité'],
                provider: UnitProvider::class
            ),
            new Post(
                openapiContext: ['description' => 'Créer une unité', 'summary' => 'Créer une unité'],
                processor: PersistProcessor::class
            ),
            new Delete(
                openapiContext: ['description' => 'Supprime une unité', 'summary' => 'Supprime une unité'],
                validationContext: ['groups' => ['delete']],
                provider: UnitProvider::class,
                processor: RemoveProcessor::class
            ),
            new Patch(
                inputFormats: ['json' => ['application/merge-patch+json']],
                openapiContext: ['description' => 'Modifie une unité', 'summary' => 'Modifie une unité'],
                provider: UnitProvider::class,
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
        order: ['code' => 'asc'],
        security: Role::GRANTED_MANAGEMENT_ADMIN
    ),
    ORM\Entity(repositoryClass: UnitRepository::class),
    UniqueEntity(fields: ['code', 'deleted'], ignoreNull: true),
    UniqueEntity(fields: ['name', 'deleted'], ignoreNull: true)
]
class Unit extends Entity {
    /** @var Collection<int, Attribute> */
    #[
        Assert\Count(exactly: 0, exactMessage: 'This unit is associated with attributes.', groups: ['delete']),
        ORM\OneToMany(mappedBy: 'unit', targetEntity: Attribute::class)
    ]
    private Collection $attributes;

    #[
        ApiProperty(description: 'Base', example: 1),
        Assert\NotBlank,
        Assert\Positive,
        ORM\Column(options: ['default' => 1]),
        Serializer\Groups(['unit-read', 'unit-write'])
    ]
    private float $base = 1;

    /** @var Collection<int, self> */
    #[
        Assert\Count(exactly: 0, exactMessage: 'This unit has children.', groups: ['delete']),
        ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)
    ]
    private Collection $children;

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

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    public function __construct() {
        $this->attributes = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function addAttribute(Attribute $attribute): self {
        if ($this->attributes->contains($attribute) === false) {
            $this->attributes->add($attribute);
            $attribute->setUnit($this);
        }
        return $this;
    }

    public function addChild(self $child): self {
        if ($this->children->contains($child) === false) {
            $this->children->add($child);
            $child->setParent($this);
        }
        return $this;
    }

    /** @return Collection<int, Attribute> */
    public function getAttributes(): Collection {
        return $this->attributes;
    }

    public function getBase(): float {
        return $this->base;
    }

    /** @return Collection<int, self> */
    public function getChildren(): Collection {
        return $this->children;
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

    public function removeAttribute(Attribute $attribute): self {
        if ($this->attributes->contains($attribute)) {
            $this->attributes->removeElement($attribute);
            if ($attribute->getUnit() === $this) {
                $attribute->setUnit(null);
            }
        }
        return $this;
    }

    public function removeChild(self $child): self {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }
        return $this;
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
        if ($this->parent === $parent) {
            return $this;
        }
        $this->parent?->removeChild($this);
        $this->parent = $parent;
        $this->parent?->addChild($this);
        return $this;
    }
}
