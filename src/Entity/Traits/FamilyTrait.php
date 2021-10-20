<?php

namespace App\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait FamilyTrait
{
    use NameTrait;
    /** @var Collection */
    private $children;
    /**
     * @Assert\Length(min=3, max=255)
     *
     * @ORM\Column(nullable=true)
     *
     * @var string|null
     */
    private $customsCode;
    private $parent;

    public function __construct() {
        $this->children = new ArrayCollection();
    }

    final public function addChild($child): self {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }
        return $this;
    }

    final public function getChildren(): Collection {
        return $this->children;
    }

    final public function getCustomsCode(): ?string {
        return $this->customsCode;
    }

    final public function getParent() {
        return $this->parent;
    }

    final public function hasParent(): bool {
        return !empty($this->parent);
    }

    final public function removeChild($child): self {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }
        return $this;
    }

    final public function setCustomsCode(?string $customsCode): self {
        $this->customsCode = $customsCode;
        return $this;
    }

    final public function setParent($parent): self {
        $this->parent = $parent;
        return $this;
    }

}