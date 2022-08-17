<?php

namespace App\Entity\Quality\Reception;

use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Component\Family;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @template-extends Reference<Family, Component>
 */
#[ORM\Entity]
class ComponentReference extends Reference {
    #[ORM\ManyToMany(targetEntity: Family::class)]
    protected Collection $families;

    #[ORM\ManyToMany(targetEntity: Component::class)]
    protected Collection $items;
}
