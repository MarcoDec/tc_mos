<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Interfaces\FileEntity;
use App\Entity\Traits\CustomsCodeTrait;
use App\Entity\Traits\FileTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class Family extends Entity implements FileEntity {
    use CustomsCodeTrait;
    use FileTrait;
    use NameTrait;

    /** @var Collection<int, self> */
    protected Collection $children;

    #[
        ApiProperty(description: 'Nom', required: true),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    /** @var null|self */
    #[
        ApiProperty(description: 'Famille parente', readableLink: false),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    protected $parent;

    #[Pure]
    public function __construct() {
        $this->children = new ArrayCollection();
    }

    final public function addChild(self $child): self {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }
        return $this;
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

    /**
     * @return Collection<int, self>
     */
    final public function getChildrenWithSelf(): Collection {
        $array = collect([$this]);
        foreach ($this->getChildren() as $child) {
            $array = $array->merge($child->getChildrenWithSelf());
        }
        return new ArrayCollection($array->values()->all());
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getParent(): ?self {
        return $this->parent;
    }

    final public function hasParent(): bool {
        return !empty($this->parent);
    }

    final public function removeChild(self $child): self {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }
        return $this;
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

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setParent(?self $parent): self {
        $this->parent = $parent;
        return $this;
    }
}
