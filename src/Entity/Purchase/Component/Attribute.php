<?php

namespace App\Entity\Purchase\Component;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Doctrine\DBAL\Types\Purchase\Component\AttributeType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Unit;
use App\Filter\EnumFilter;
use App\Filter\RelationFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection as LaravelCollection;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: EnumFilter::class, properties: ['type']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['unit']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['description' => 'partial', 'name' => 'partial']),
    ApiFilter(filterClass: OrderFilter::class, properties: ['name', 'type', 'unit.name']),
    ApiResource(
        description: 'Attribut',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les attributs',
                    'summary' => 'Récupère les attributs',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un attribut',
                    'summary' => 'Créer un attribut',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un attribut',
                    'summary' => 'Supprime un attribut',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un attribut',
                    'summary' => 'Modifie un attribut',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:attribute'],
            'openapi_definition_name' => 'Attribute-write'
        ],
        normalizationContext: [
            'groups' => ['read:attribute', 'read:id'],
            'openapi_definition_name' => 'Attribute-read',
            'skip_null_values' => false
        ],
        order: ['name' => 'asc'],
        paginationClientEnabled: true
    ),
    ORM\Entity
]
class Attribute extends Entity {
    /** @var Collection<int, ComponentAttribute> */
    #[ORM\OneToMany(mappedBy: 'attribute', targetEntity: ComponentAttribute::class, cascade: ['remove'])]
    private Collection $attributes;

    #[
        ApiProperty(description: 'Nom', required: false, example: 'Longueur de l\'embout'),
        Assert\Length(min: 3, max: 255),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:attribute', 'write:attribute'])
    ]
    private ?string $description = null;

    /** @var Collection<int, Family> */
    #[
        ApiProperty(description: 'Familles', readableLink: false, required: true, example: ['/api/component-families/1', '/api/component-families/2']),
        ORM\ManyToMany(targetEntity: Family::class, inversedBy: 'attributes'),
        Serializer\Groups(['read:attribute'])
    ]
    private Collection $families;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Longueur'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:attribute', 'write:attribute'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Type', example: AttributeType::TYPE_TEXT, openapiContext: ['enum' => AttributeType::TYPES]),
        Assert\Choice(choices: AttributeType::TYPES),
        ORM\Column(type: 'attribute', options: ['default' => AttributeType::TYPE_TEXT]),
        Serializer\Groups(['read:attribute', 'write:attribute'])
    ]
    private string $type = AttributeType::TYPE_TEXT;

    #[
        ApiProperty(description: 'Unité', readableLink: false, required: false, example: '/api/units/1'),
        ORM\ManyToOne(fetch: 'EAGER'),
        Serializer\Groups(['read:attribute', 'write:attribute'])
    ]
    private ?Unit $unit;

    public function __construct() {
        $this->attributes = new ArrayCollection();
        $this->families = new ArrayCollection();
    }

    final public function addAttribute(ComponentAttribute $attribute): self {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes->add($attribute);
            $attribute->setAttribute($this);
        }
        return $this;
    }

    final public function addFamily(Family $family): self {
        if (!$this->families->contains($family)) {
            $this->families->add($family);
            $family->addAttribute($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, ComponentAttribute>
     */
    final public function getAttributes(): Collection {
        return $this->attributes;
    }

    final public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @return Collection<int, Family>
     */
    final public function getFamilies(): Collection {
        return $this->families;
    }

    /**
     * @return LaravelCollection<int, null|string>
     */
    #[Serializer\Groups(['read:attribute'])]
    final public function getFamiliesName(): LaravelCollection {
        return collect($this->families)->map->getFullName()->sort()->values();
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getType(): string {
        return $this->type;
    }

    final public function getUnit(): ?Unit {
        return $this->unit;
    }

    final public function removeAttribute(ComponentAttribute $attribute): self {
        if ($this->attributes->contains($attribute)) {
            $this->attributes->removeElement($attribute);
            if ($attribute->getAttribute() === $this) {
                $attribute->setAttribute(null);
            }
        }
        return $this;
    }

    final public function removeFamily(Family $family): self {
        if ($this->families->contains($family)) {
            $this->families->removeElement($family);
            $family->removeAttribute($this);
        }
        return $this;
    }

    final public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setType(string $type): self {
        $this->type = $type;
        return $this;
    }

    final public function setUnit(?Unit $unit): self {
        $this->unit = $unit;
        return $this;
    }
}
