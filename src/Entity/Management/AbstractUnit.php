<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\EntityId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Illuminate\Support\Collection as LaravelCollection;
use LogicException;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    Gedmo\Tree(type: 'nested'),
    ORM\MappedSuperclass(repositoryClass: NestedTreeRepository::class)
]
class AbstractUnit extends EntityId {
    final public const UNIT_CODE_MAX_LENGTH = 6;

    #[
        ApiProperty(description: 'Base', required: true, example: 1),
        Assert\NotBlank,
        Assert\Positive,
        ORM\Column(options: ['default' => 1]),
        Serializer\Groups(['read:currency', 'read:unit', 'write:unit'])
    ]
    protected float $base = 1;

    /** @var Collection<int, static> */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
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
        Gedmo\TreeLeft,
        ORM\Column(options: ['default' => 1, 'unsigned' => true])
    ]
    protected int $lft = 1;

    #[
        Gedmo\TreeLevel,
        ORM\Column(options: ['default' => 0, 'unsigned' => true])
    ]
    protected int $lvl = 0;

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

    protected ?NestedTreeRepository $repo = null;

    #[
        Gedmo\TreeRight,
        ORM\Column(options: ['default' => 2, 'unsigned' => true])
    ]
    protected int $rgt = 2;

    /** @var null|static */
    protected $root;

    public function __construct() {
        $this->children = new ArrayCollection();
    }

    /**
     * @param static $child
     */
    final public function addChild(self $child): self {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }
        return $this;
    }

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

    final public function getConvertorDistance(self $unit): float {
        $distance = $this->getDistance($unit);
        return $this->isLessThan($unit) ? 1 / $distance : $distance;
    }

    final public function getLess(self $unit): self {
        return $this->base < $unit->base ? $this : $unit;
    }

    final public function getLft(): int {
        return $this->lft;
    }

    final public function getLvl(): int {
        return $this->lvl;
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

    #[Serializer\Groups(['read:currency', 'read:unit'])]
    final public function getParentId(): int {
        return $this->parent?->getId() ?? 0;
    }

    final public function getRepo(): ?NestedTreeRepository {
        return $this->repo;
    }

    final public function getRgt(): int {
        return $this->rgt;
    }

    /**
     * @return null|static
     */
    final public function getRoot(): ?self {
        return $this->root;
    }

    #[Serializer\Groups(['read:unit:option'])]
    final public function getText(): ?string {
        return $this->getCode();
    }

    final public function has(?self $unit): bool {
        return $unit !== null && $this->getFamily()->contains(static fn (self $member): bool => $member->getId() === $unit->getId());
    }

    final public function isLessThan(self $unit): bool {
        return $this->getLess($unit) === $this;
    }

    /**
     * @param static $child
     */
    final public function removeChild(self $child): self {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            if ($child->getParent() === $this) {
                $child->setParent(null);
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

    final public function setLft(int $lft): self {
        $this->lft = $lft;
        return $this;
    }

    final public function setLvl(int $lvl): self {
        $this->lvl = $lvl;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    /**
     * @param null|static $parent
     *
     * @return $this
     */
    final public function setParent(?self $parent): self {
        if (empty($parent)) {
            $this->parent?->removeChild($this);
        } else {
            $parent->addChild($this);
        }
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setRepo(?NestedTreeRepository $repo): self {
        $this->repo = $repo;
        return $this;
    }

    final public function setRgt(int $rgt): self {
        $this->rgt = $rgt;
        return $this;
    }

    /**
     * @param null|static $root
     */
    final public function setRoot(?self $root): self {
        $this->root = $root;
        return $this;
    }

    /**
     * @return LaravelCollection<int, self>
     */
    private function getDepthChildren(): LaravelCollection {
        return collect($this->repo?->children(node: $this, includeNode: true))->values();
    }

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
        if ($this->root?->getId() === $this->getId()) {
            return $this->getDepthChildren();
        }
        if (empty($this->root)) {
            /** @var self[] $empty */
            $empty = [];
            return collect($empty);
        }
        return $this->root->getFamily();
    }
}
