<?php

namespace App\Entity\Quality\Reception;

use App\Entity\Project\Product\Family;
use App\Entity\Project\Product\Product;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @template-extends Reference<Family, Product>
 */
#[ORM\Entity]
class ProductReference extends Reference {
    #[ORM\ManyToMany(targetEntity: Family::class)]
    protected Collection $families;

    #[ORM\ManyToMany(targetEntity: Product::class)]
    protected Collection $items;
}
