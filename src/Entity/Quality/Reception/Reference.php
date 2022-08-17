<?php

namespace App\Entity\Quality\Reception;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\ItemType;
use App\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @template F of \App\Entity\Purchase\Component\Family|\App\Entity\Project\Product\Family
 * @template I of \App\Entity\Purchase\Component\Component|\App\Entity\Project\Product\Product
 */
#[
    ORM\DiscriminatorColumn(name: 'type', type: 'item_type'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE')
]
abstract class Reference extends Entity {
    public const KIND_DIM = 'Dimensionnel';
    public const KIND_DOC = 'Documentaire';
    public const KIND_GON = 'GO/NOGO';
    public const KIND_QTE = 'Quantitatif';
    public const KIND_VIS = 'Visuel';
    public const KINDS = [self::KIND_DOC, self::KIND_DIM, self::KIND_GON, self::KIND_VIS, self::KIND_QTE];
    public const TYPES = [ItemType::TYPE_COMPONENT => ComponentReference::class, ItemType::TYPE_PRODUCT => ProductReference::class];

    /** @var Collection<int, F> */
    protected Collection $families;

    /** @var Collection<int, I> */
    protected Collection $items;

    #[
        ApiProperty(description: 'Nom ', required: true, example: 'Dimensions'),
        ORM\Column(length: 40)
    ]
    private ?string $name = null;

    public function __construct() {
        $this->families = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    /**
     * @param F $family
     *
     * @return $this
     */
    final public function addFamily($family): self {
        if (!$this->families->contains($family)) {
            $this->families->add($family);
        }
        return $this;
    }

    /**
     * @param I $item
     *
     * @return $this
     */
    final public function addItem($item): self {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }
        return $this;
    }

    /**
     * @return Collection<int, F>
     */
    final public function getFamilies(): Collection {
        return $this->families;
    }

    /**
     * @return Collection<int, I>
     */
    final public function getItems(): Collection {
        return $this->items;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param F $family
     *
     * @return $this
     */
    final public function removeFamily($family): self {
        if ($this->families->contains($family)) {
            $this->families->removeElement($family);
        }
        return $this;
    }

    /**
     * @param I $item
     *
     * @return $this
     */
    final public function removeItem($item): self {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
        }
        return $this;
    }

    /**
     * @return $this
     */
    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
