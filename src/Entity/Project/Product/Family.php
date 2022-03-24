<?php

namespace App\Entity\Project\Product;

use App\Entity\Family as AbstractFamily;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity,
    ORM\Table(name: 'product_family'),
    UniqueEntity(['name', 'parent'])
]
class Family extends AbstractFamily {
    /** @var Collection<int, self> */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['remove'])]
    protected Collection $children;

    #[
        Assert\Length(min: 3, max: 20),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    /** @var null|self */
    #[
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    protected $parent;

    #[Serializer\Groups(['read:file'])]
    final public function getFilepath(): ?string {
        return parent::getFilepath();
    }
}
