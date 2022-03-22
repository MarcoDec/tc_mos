<?php

namespace App\Entity\Project\Product;

use App\Entity\Family as AbstractFamily;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity,
    ORM\Table(name: 'product_family'),
    UniqueEntity(['name', 'parent'])
]
class Family extends AbstractFamily {
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['remove'])]
    protected Collection $children;

    #[
        Assert\Length(min: 3, max: 20),
        Assert\NotBlank,
        ORM\Column(length: 30)
    ]
    protected ?string $name = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    protected $parent;

    final public function getFilepath(): ?string {
        return parent::getFilepath();
    }
}
