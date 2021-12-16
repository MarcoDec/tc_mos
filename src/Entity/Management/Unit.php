<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
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
            'groups' => ['write:name', 'write:unit'],
            'openapi_definition_name' => 'Unit-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:unit'],
            'openapi_definition_name' => 'Unit-read'
        ]
    ),
    ORM\Entity,
    UniqueEntity('code'),
    UniqueEntity('name')
]
class Unit extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Gramme'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    /** @var Collection<int, self> */
    #[
        ApiProperty(description: 'Unités enfant'),
        ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['remove']),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    private Collection $children;

    #[
        ApiProperty(description: 'Code ', required: true, example: 'g'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Unité parente', readableLink: false, example: '/api/unit/3'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    private ?self $parent = null;

    public function __construct() {
        $this->children = new ArrayCollection();
    }

    public function addChild(self $child): self {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection {
        return $this->children;
    }

    final public function getCode(): ?string {
        return $this->code;
    }

    public function getParent(): ?self {
        return $this->parent;
    }

    public function removeChild(self $child): self {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    public function setParent(?self $parent): self {
        $this->parent = $parent;

        return $this;
    }
}
