<?php

namespace App\Entity\Purchase\Component;

use App\Entity\Family as AbstractFamily;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity,
    ORM\Table(name: 'component_family'),
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

    #[
        Assert\Length(exactly: 3),
        Assert\NotBlank,
        ORM\Column(type: 'char', length: 3, options: ['charset' => 'ascii'])
    ]
    private ?string $code = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $copperable = false;

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getCopperable(): ?bool {
        return $this->copperable;
    }

    final public function getFilepath(): ?string {
        return parent::getFilepath();
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setCopperable(bool $copperable): self {
        $this->copperable = $copperable;
        return $this;
    }
}
