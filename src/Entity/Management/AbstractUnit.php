<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Entity;
use App\Entity\Traits\NameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection as LaravelCollection;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class AbstractUnit extends Entity {
    use NameTrait;

    /** @var Collection<int, mixed> */
    protected Collection $children;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Gramme'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    /** @var null|self */
    #[
        ApiProperty(description: 'Parent ', readableLink: false, example: '/api/units/1'),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    protected $parent;

    #[
        ApiProperty(description: 'Base', required: true, example: 1),
        Assert\NotBlank,
        Assert\Positive,
        ORM\Column(options: ['default' => 1]),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    private float $base = 1;

    #[
        ApiProperty(description: 'Code ', required: true, example: 'g'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    private ?string $code = null;

    #[Pure]
    public function __construct() {
        $this->children = new ArrayCollection();
    }

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
     * @return Collection<int, mixed>
     */
    final public function getChildren(): Collection {
        return $this->children;
    }

    final public function getCode(): ?string {
        return $this->code;
    }

    #[Pure]
    final public function getConvertorDistance(self $unit): float {
        $distance = $this->getDistance($unit);
        return $this->isLessThan($unit) ? 1 / $distance : $distance;
    }

    public function getName(): ?string {
        return $this->name;
    }

    final public function getParent(): ?self {
        return $this->parent;
    }

    final public function has(?self $unit): bool {
        return $unit !== null && $this->getFamily()->contains(static fn (self $member): bool => $member->getId() === $unit->getId());
    }

    #[Pure]
    final public function isLessThan(self $unit): bool {
        return $this->getLess($unit) === $this;
    }

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

    final public function setParent(?self $parent): self {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return LaravelCollection<int, self>
     */
    private function getDepthChildren(): LaravelCollection {
        return collect($this->children->getValues())
            ->map(static fn (self $child): array => $child->getDepthChildren()->push($child)->values()->all())
            ->flatten()
            ->unique->getId()
            ->values();
    }

    #[Pure]
    private function getDistance(self $unit): float {
        return $this->getDistanceBase() * $unit->getDistanceBase();
    }

    private function getDistanceBase(): float {
        return $this->base > 1 ? $this->base : 1 / $this->base;
    }

    /**
     * @return LaravelCollection<int, self>
     */
    private function getFamily(): LaravelCollection {
        $root = $this->getRoot();
        return $root->getDepthChildren()->push($root)->unique->getId()->values();
    }

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
