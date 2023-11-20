<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Collection;
use App\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use LogicException;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class AbstractUnit extends Entity {
    final public const UNIT_CODE_MAX_LENGTH = 6;

    /** @var DoctrineCollection<int, static> */
    protected DoctrineCollection $children;

    #[
        ApiProperty(description: 'Code ', required: true, example: 'g'),
        Assert\Length(min: 1, max: self::UNIT_CODE_MAX_LENGTH),
        Assert\NotBlank,
        ORM\Column(length: self::UNIT_CODE_MAX_LENGTH, options: ['collation' => 'utf8mb3_bin']),
        Serializer\Groups(['read:currency', 'read:unit', 'read:unit:option', 'write:unit'])
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
   #[
        ApiProperty(description: "unité parente", example: '/api/units/1'),
        ORM\ManyToOne(targetEntity: Unit::class, fetch: 'EAGER', inversedBy: 'children'),
        Serializer\Groups(['read:unit'])
   ]
    protected ?AbstractUnit $parent;

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
     * @return DoctrineCollection<int, static>
     */
    final public function getChildren(): DoctrineCollection {
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
    public function getText(): ?string {
        return $this->getCode();
    }

    /**
     * @param null|static $unit
     */
    final public function has(?self $unit): bool {
       $unitFamily = $this->getFamily();
       $test = $unit !== null && ($unit->getCode() == $this->getCode()||$unitFamily->contains(static fn (self $member): bool => $member->getId() === $unit->getId()));
       return $test;
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
     * @return Collection<int, static>
     */
    private function getDepthChildren(): Collection {
        /**
         * @param static $child
         *
         * @return Collection<int, static>
         */
        $mapper = static fn (self $child): Collection => $child->getDepthChildren()->push($child);
        /** @var Collection<int, static> $children */
        $children = Collection::collect($this->getChildren()->getValues())->map($mapper)->flatten();
        return $children->unique(static fn (self $child): ?int => $child->getId());
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
     * @return Collection<int, static>
     */
    private function getFamily(): Collection {
        return ($root = $this->getRoot())
            ->getDepthChildren()
            ->push($root)
            ->unique(static fn (self $child): ?int => $child->getId());
    }

    /**
     * @return static
     */
    private function getRoot(): self {
        $root = $this;
        while ($root->parent !== null) {
            $root = $root->parent;
        }
        return $root;
    }
}
