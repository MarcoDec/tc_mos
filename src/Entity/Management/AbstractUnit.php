<?php

namespace App\Entity\Management;

use App\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection as LaravelCollection;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class AbstractUnit extends Entity {
    /** @var Collection<int, static> */
    protected Collection $children;

    #[
        Assert\NotBlank,
        ORM\Column(length: 3)
    ]
    protected ?string $code = null;

    #[
        Assert\NotBlank,
        ORM\Column(length: 20)
    ]
    protected ?string $name = null;

    /** @var null|static */
    protected $parent;

    #[
        Assert\NotBlank,
        Assert\Positive,
        ORM\Column(options: ['default' => 1])
    ]
    private float $base = 1;

    #[Pure]
    public function __construct() {
        $this->children = new ArrayCollection();
    }

    /**
     * @param static $children
     */
    final public function addChild(self $children): self {
        if (!$this->children->contains($children)) {
            $this->children->add($children);
            $children->setParent($this);
        }
        return $this;
    }

    final public function getBase(): float {
        return $this->base;
    }

    /**
     * @return Collection<int, static>
     */
    final public function getChildren(): Collection {
        return $this->children;
    }

    final public function getCode(): ?string {
        return $this->code;
    }

    /**
     * @param static $unit
     */
    #[Pure]
    final public function getConvertorDistance(self $unit): float {
        $distance = $this->getDistance($unit);
        return $this->isLessThan($unit) ? 1 / $distance : $distance;
    }

    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @return null|static
     */
    final public function getParent(): ?self {
        return $this->parent;
    }

    /**
     * @param null|static $unit
     */
    final public function has(?self $unit): bool {
        return $unit !== null && $this->getFamily()->contains(static fn (self $member): bool => $member->getId() === $unit->getId());
    }

    /**
     * @param static $unit
     */
    #[Pure]
    final public function isLessThan(self $unit): bool {
        return $this->getLess($unit) === $this;
    }

    /**
     * @param static $children
     */
    final public function removeChild(self $children): self {
        if ($this->children->contains($children)) {
            $this->children->removeElement($children);
            if ($children->getParent() === $this) {
                $children->setParent(null);
            }
        }
        return $this;
    }

    final public function setBase(float $base): self {
        $this->base = $base;
        return $this;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
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

    /**
     * @return LaravelCollection<int, static>
     */
    private function getDepthChildren(): LaravelCollection {
        /** @var LaravelCollection<int, static> $children */
        $children = collect($this->children->getValues())
            ->map(static fn (self $child): array => $child->getDepthChildren()->push($child)->values()->all())
            ->flatten()
            ->unique->getId();
        return $children->values();
    }

    /**
     * @param static $unit
     */
    #[Pure]
    private function getDistance(self $unit): float {
        return $this->getDistanceBase() * $unit->getDistanceBase();
    }

    private function getDistanceBase(): float {
        return $this->base > 1 ? $this->base : 1 / $this->base;
    }

    /**
     * @return LaravelCollection<int, static>
     */
    private function getFamily(): LaravelCollection {
        /** @var static $root */
        $root = $this->getRoot();
        /** @var LaravelCollection<int, static> $children */
        $children = $root->getDepthChildren()->push($root)->unique->getId();
        return $children->values();
    }

    /**
     * @param static $unit
     */
    private function getLess(self $unit): self {
        return $this->base < $unit->base ? $this : $unit;
    }

    private function getRoot(): self {
        $root = $this;
        while ($root->parent !== null) {
            $root = $root->parent;
        }
        return $root;
    }
}
