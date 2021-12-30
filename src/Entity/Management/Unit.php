<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Traits\CodeTrait;
use App\Entity\Traits\NameTrait;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'name' => 'partial',
        'code' => 'partial'
    ]),
    ApiResource(
        description: 'Unité',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les unités',
                    'summary' => 'Récupère les unités',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une unité',
                    'summary' => 'Créer une unité',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une unité',
                    'summary' => 'Supprime une unité',
                ]
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une unité',
                    'summary' => 'Récupère une unité',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une unité',
                    'summary' => 'Modifie une unité',
                ]
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name', 'write:unit', 'write:measure'],
            'openapi_definition_name' => 'Unit-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:unit', 'read:measure'],
            'openapi_definition_name' => 'Unit-read'
        ]
    ),
    ORM\Entity,
    UniqueEntity('code'),
    UniqueEntity('name')
]
class Unit extends Entity {
    use CodeTrait;
    use NameTrait;

    #[
        ApiProperty(description: 'Code', required: true, example: 'g'),
        Assert\Length(max: 2),
        Assert\NotBlank,
        ORM\Column(length: 2),
        Serializer\Groups(['read:code', 'write:code'])
    ]
    protected ?string $code = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Gramme'),
        Assert\NotBlank,
        ORM\Column(type: 'string'),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Base', required: true, example: '0.845'),
        Assert\NotBlank,
        ORM\Column(options: ['default' => 0], type: 'float'),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    private ?float $base = 0;

    /** @var Collection<int, self> */
    #[
        ApiProperty(description: 'Unités enfant', readableLink: false, example: ['/api/units/3', '/api/units/4']),
        ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['remove']),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    private Collection $children;

    #[
        ApiProperty(description: 'Dénominateur', readableLink: false, example: '/api/units/3'),
        ORM\OneToOne(targetEntity: self::class),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private ?Unit $denominator = null;

    #[
        ApiProperty(description: 'Unité parente', readableLink: false, example: '/api/units/1'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    private ?self $parent = null;

    public function __construct() {
        $this->children = new ArrayCollection();
    }

    final public function addChild(self $child): self {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    final public function getBase(): ?float {
        return $this->base;
    }

    /**
     * @return Collection<int, self>
     */
    final public function getChildren(): Collection {
        return $this->children;
    }

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getDenominator(): ?self {
        return $this->denominator;
    }

    /**
     * Returns the multiplicator from the upper parent Unit.
     *
     * @return float
     */
    final public function getMultiplicatorFromBase(): ?float {
        if (null === $this->parent) {
            return 1;
        }

        $multiplicator = $this->base == 0 ? 1 : $this->base;
        $parent = $this->parent;

        for ($parent; null !== $parent->parent; $parent = $parent->parent) {
            $multiplicator *= $parent->base == 0 ? 1 : $parent->base;
        }

        return $multiplicator;
    }

    final public function getParent(): ?self {
        return $this->parent;
    }

    /**
     * Returns the smallest unit in the tree.
     */
    final public function getTopUnit(): self {
        $parent = $this->getParent();

        if (null === $parent) {
            return $this;
        }

        for ($parent; null !== $parent->getParent(); $parent = $parent->getParent());

        return $parent;
    }

    final public function removeChild(self $child): self {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    final public function setBase(?float $base): self {
        $this->base = $base;
        return $this;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setDenominator(self $denominator): self {
        $this->denominator = $denominator;
        $this->code = $this->code.'/'.$denominator->getCode();

        return $this;
    }

    final public function setParent(?self $parent): self {
        $this->parent = $parent;

        return $this;
    }
}
