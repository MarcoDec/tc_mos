<?php

namespace App\Entity\Project\Product;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Entity;
use App\Entity\Traits\NameTrait;
use App\Filter\RelationFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['parent']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['customsCode' => 'partial', 'name' => 'partial']),
    ApiResource(
        description: 'Famille de produit',
        itemOperations: [
            'delete' => [],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => []
        ],
        shortName: 'ProductFamily',
        denormalizationContext: ['groups' => ['write:family', 'write:name'], 'openapi_definition_name' => 'ProductFamily-write'],
        normalizationContext: ['groups' => ['read:family', 'read:id', 'read:name'], 'openapi_definition_name' => 'ProductFamily-read'],
        paginationEnabled: false
    ),
    ORM\Entity,
    ORM\Table(name: 'product_family')
]
class Family extends Entity {
    use NameTrait;

    /** @var Collection<int, self> */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $children;

    #[
        ApiProperty(description: 'Code douanier', example: '8544300089'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    private ?string $customsCode;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Faisceaux'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    private string $name;

    #[
        ApiProperty(description: 'Famille parente', example: '/api/product-families/1'),
        ORM\ManyToOne(targetEntity: self::class, fetch: 'EAGER', inversedBy: 'children'),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    private ?self $parent;

    #[Pure]
    public function __construct() {
        $this->children = new ArrayCollection();
    }

    final public function addChildren(self $children): self {
        if (!$this->children->contains($children)) {
            $this->children->add($children);
            $children->setParent($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    final public function getChildren(): Collection {
        return $this->children;
    }

    final public function getCustomsCode(): ?string {
        return $this->customsCode;
    }

    final public function getParent(): ?self {
        return $this->parent;
    }

    final public function removeChildren(self $children): self {
        if ($this->children->contains($children)) {
            $this->children->removeElement($children);
            if ($children->getParent() === $this) {
                $children->setParent(null);
            }
        }
        return $this;
    }

    final public function setCustomsCode(?string $customsCode): self {
        $this->customsCode = $customsCode;
        return $this;
    }

    final public function setParent(?self $parent): self {
        $this->parent = $parent;
        return $this;
    }
}
