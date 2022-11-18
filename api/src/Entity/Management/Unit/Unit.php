<?php

declare(strict_types=1);

namespace App\Entity\Management\Unit;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\Type\Hr\Employee\Role;
use App\Doctrine\Type\Management\Unit\UnitType;
use App\Dto\Management\Unit\UnitGenerator;
use App\Entity\Entity;
use App\Entity\Purchase\Component\Attribute;
use App\Repository\Management\UnitRepository;
use App\State\Management\UnitProvider;
use App\State\PersistProcessor;
use App\State\RemoveProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
                input: UnitGenerator::class,
                processor: PersistProcessor::class
            ),
            new Get(
                openapiContext: ['description' => 'Récupère une unité', 'summary' => 'Récupère une unité'],
                provider: UnitProvider::class
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
                denormalizationContext: ['groups' => ['unit-write']],
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
        order: ['code' => 'asc'],
        security: Role::GRANTED_MANAGEMENT_ADMIN
    ),
    Gedmo\Tree(type: 'nested'),
    ORM\DiscriminatorColumn(name: 'type', type: 'unit'),
    ORM\DiscriminatorMap(UnitType::TYPES),
    ORM\Entity(repositoryClass: UnitRepository::class),
    ORM\InheritanceType('SINGLE_TABLE'),
    UniqueEntity(fields: ['code', 'deleted'], ignoreNull: false),
    UniqueEntity(fields: ['name', 'deleted'], ignoreNull: false)
]
abstract class Unit extends Entity {
    /** @var Collection<int, Attribute> */
    #[
        Assert\Count(exactly: 0, exactMessage: 'This unit is associated with attributes.', groups: ['delete']),
        ORM\OneToMany(mappedBy: 'unit', targetEntity: Attribute::class)
    ]
    private Collection $attributes;

    #[
        ApiProperty(description: 'Base', example: 1),
        Assert\IdenticalTo(
            value: 1.0,
            message: 'A unit without a parent must have a base equal to {{ compared_value }}.',
            groups: ['base']
        ),
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

    #[Gedmo\TreeLeft, ORM\Column]
    private ?int $lft = null;

    #[Gedmo\TreeLevel, ORM\Column]
    private ?int $lvl = null;

    #[
        ApiProperty(description: 'Nom', example: 'Gramme'),
        Assert\Length(min: 5, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50),
        Serializer\Groups(['unit-read', 'unit-write'])
    ]
    private ?string $name = null;

    #[Gedmo\TreeParent, ORM\ManyToOne(targetEntity: self::class)]
    private ?self $parent = null;

    #[Gedmo\TreeRight, ORM\Column]
    private ?int $rgt = null;

    #[Gedmo\TreeRoot, ORM\ManyToOne(targetEntity: self::class)]
    private ?self $root = null;

    public function __construct() {
        $this->attributes = new ArrayCollection();
    }

    public function addAttribute(Attribute $attribute): self {
        if ($this->attributes->contains($attribute) === false) {
            $this->attributes->add($attribute);
            $attribute->setUnit($this);
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

    public function getCode(): ?string {
        return $this->code;
    }

    public function getLft(): ?int {
        return $this->lft;
    }

    public function getLvl(): ?int {
        return $this->lvl;
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

    public function getRgt(): ?int {
        return $this->rgt;
    }

    public function getRoot(): ?self {
        return $this->root;
    }

    #[ApiProperty(required: true), Serializer\Groups('unit-option')]
    public function getText(): ?string {
        return $this->code;
    }

    #[Assert\IsFalse(message: 'This unit has children.', groups: ['delete'])]
    public function hasChildren(): bool {
        return $this->rgt - $this->lft > 1;
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

    public function setBase(float $base): self {
        $this->base = $base;
        return $this;
    }

    public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    public function setLft(?int $lft): self {
        $this->lft = $lft;
        return $this;
    }

    public function setLvl(?int $lvl): self {
        $this->lvl = $lvl;
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

    public function setRgt(?int $rgt): self {
        $this->rgt = $rgt;
        return $this;
    }

    public function setRoot(?self $root): self {
        $this->root = $root;
        return $this;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void {
        if (empty($this->parent)) {
            $context->getViolations()->addAll($context->getValidator()->validate(value: $this, groups: ['base']));
        }
    }
}
