<?php

namespace App\Entity\Quality\Reception;

use App\Entity\Entity;
use App\Entity\Project\Product\Family as ProductFamily;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Component\Family as ComponentFamily;
use App\Entity\Traits\NameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Reference extends Entity {
    use NameTrait;

    public const KIND_DIM = 'Dimensionnel';
    public const KIND_DOC = 'Documentaire';
    public const KIND_GON = 'GO/NOGO';
    public const KIND_QTE = 'Quantitatif';
    public const KIND_VIS = 'Visuel';
    public const KINDS = [self::KIND_DOC, self::KIND_DIM, self::KIND_GON, self::KIND_VIS, self::KIND_QTE];
    public const TYPES = [
        'product' => ProductReference::class,
        'component' => ComponentReference::class,
    ];

    /**
     * @var Collection<int, ComponentFamily|ProductFamily>
     */
    protected ArrayCollection $families;

    /**
     * @var Collection<int, Component|Product>
     */
    protected ArrayCollection $items;

    public function __construct() {
        $this->families = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    abstract public function addFamily(object $family): self;

    abstract public function addItem(object $item): self;

    abstract public function getItemType(): string;

    abstract public function removeFamily(object $family): self;

    abstract public function removeItem(object $item): self;

    /**
     * @return Collection<int, ComponentFamily|ProductFamily>
     */
    public function getFamilies(): Collection {
        return $this->families;
    }

    /**
     * @return Collection<int, Component|Product>
     */
    public function getItems(): Collection {
        return $this->items;
    }
}
