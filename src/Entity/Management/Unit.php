<?php

namespace App\Entity\Management;

use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @method self            addChild(self $children)
 * @method Collection<int, self> getChildren()
 * @method float           getConvertorDistance(self $unit)
 * @method null|self       getParent()
 * @method bool            has(null|self $unit)
 * @method bool            isLessThan(self $unit)
 * @method self            removeChild(self $children)
 * @method self            setBase(float $base)
 * @method self            setCode(null|string $code)
 * @method self            setName(null|string $name)
 * @method self            setParent(null|self $parent)
 */
#[
    ORM\Entity,
    UniqueEntity('code'),
    UniqueEntity('name')
]
class Unit extends AbstractUnit {
    /** @var Collection<int, self> */
    #[
        ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class),
        Serializer\Groups(['read:unit'])
    ]
    protected Collection $children;

    /** @var null|self */
    #[
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    protected $parent;
}
