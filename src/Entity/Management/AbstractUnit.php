<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\EntityId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection as LaravelCollection;
use JetBrains\PhpStorm\Pure;
use LogicException;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class AbstractUnit extends EntityId {
    final public const UNIT_CODE_MAX_LENGTH = 6;

    /** @var Collection<int, static> */
    protected Collection $children;

    #[
        ApiProperty(description: 'Code ', required: true, example: 'g'),
        Assert\Length(min: 1, max: self::UNIT_CODE_MAX_LENGTH),
        Assert\NotBlank,
        ORM\Column(length: self::UNIT_CODE_MAX_LENGTH, options: ['collation' => 'utf8_bin']),
        Serializer\Groups(['read:currency', 'read:unit', 'write:unit'])
    ]
    protected ?string $code = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Gramme'),
        Assert\Length(min: 5, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    protected ?string $name = null;

    /** @var null|static */
    protected $parent;

    #[
        ApiProperty(description: 'Base', required: true, example: 1),
        Assert\NotBlank,
        Assert\Positive,
        ORM\Column(options: ['default' => 1]),
        Serializer\Groups(['read:currency', 'read:unit', 'write:unit'])
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

    /**
     * @param null|static $unit
     */
    final public function assertSameAs(?self $unit): void {
        if ($unit === null || $unit->code === null) {
            throw new LogicException('No code defined.');
        }
        if (!$this->has($unit)) {
            throw new LogicException("Units {$this->code} and {$unit->code} aren't on the same family.");
        }
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

    /**
     * @param static $unit
     */
    final public function getLess(self $unit): self {
        return $this->base < $unit->base ? $this : $unit;
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

    #[
        Pure,
        Serializer\Groups(['read:currency', 'read:unit'])
    ]
    final public function getParentId(): int {
        return $this->parent?->getId() ?? 0;
    }

    #[Serializer\Groups(['read:unit:option'])]
    final public function getText(): ?string {
        return $this->getCode();
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
        $children = collect($this->getChildren()->getValues())
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

    private function getRoot(): self {
        $root = $this;
        while ($root->parent !== null) {
            $root = $root->parent;
        }
        return $root;
    }
}
