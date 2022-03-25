<?php

namespace App\Entity\Management;

use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity,
    UniqueEntity('code'),
    UniqueEntity('name')
]
class Unit extends AbstractUnit {
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    protected Collection $children;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    protected $parent;
}
