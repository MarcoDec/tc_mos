<?php

namespace App\Entity\Quality\Reception;

use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Component\Family;
use App\Entity\Traits\ReferenceTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ComponentReference extends Reference {
    use ReferenceTrait {
      ReferenceTrait::__construct as private __tConstruct;
   }

    /** @var Collection<int, object> */
    #[ORM\ManyToMany(targetEntity: Family::class, inversedBy: 'references')]
    protected ArrayCollection $families;

    /** @var Collection<int, object> */
    #[ORM\ManyToMany(targetEntity: Component::class, inversedBy: 'references')]
    protected ArrayCollection $items;

    public function __construct() {
        parent::__construct();
        $this->__tConstruct();
    }

    final public function addFamily(object $family): self {
        if (!$family instanceof Family) {
            return $this;
        }

        if (!$this->families->contains($family)) {
            $this->families->add($family);
            $family->addReference($this);
        }

        return $this;
    }

    final public function addItem(object $item): self {
        if (!$item instanceof Component) {
            return $this;
        }

        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->addReference($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, mixed>
     */
    final public function getFamilies(): Collection {
        return $this->families;
    }

    /**
     * @return Collection<int, mixed>
     */
    final public function getItems(): Collection {
        return $this->items;
    }

    final public function getItemType(): string {
        return 'Component';
    }

    final public function removeFamily(object $family): self {
        if (!$family instanceof Family) {
            return $this;
        }

        if ($this->families->contains($family)) {
            $this->families->removeElement($family);
            if ($family->getReferences()->contains($this)) {
                $family->removeReference($this);
            }
        }

        return $this;
    }

    final public function removeItem(object $item): self {
        if (!$item instanceof Component) {
            return $this;
        }

        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            if ($item->getReferences()->contains($this)) {
                $item->removeReference($this);
            }
        }
        return $this;
    }
}
