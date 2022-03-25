<?php

namespace App\Entity;

use App\Entity\Interfaces\FileEntity;
use App\Entity\Traits\FileTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class Family extends Entity implements FileEntity {
    use FileTrait;

    /** @var Collection<int, static> */
    protected Collection $children;

    protected ?string $name = null;

    /** @var null|static */
    protected $parent;

    #[
        Assert\Length(min: 4, max: 10),
        ORM\Column(length: 10, nullable: true, options: ['charset' => 'ascii'])
    ]
    private ?string $customsCode = null;

    #[Pure]
    public function __construct() {
        $this->children = new ArrayCollection();
    }

    /**
     * @param static $children
     */
    final public function addChildren(self $children): self {
        if (!$this->children->contains($children)) {
            $this->children->add($children);
            $children->setParent($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, static>
     */
    final public function getChildren(): Collection {
        return $this->children;
    }

    final public function getCustomsCode(): ?string {
        return $this->customsCode;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getParent(): ?self {
        return $this->parent;
    }

    /**
     * @param static $children
     */
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

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    /**
     * @param null|static $parent
     */
    final public function setParent(?self $parent): self {
        $this->parent = $parent;
        return $this;
    }
}
