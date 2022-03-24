<?php

namespace App\Entity\Purchase\Component;

use App\Entity\Family as AbstractFamily;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity,
    ORM\Table(name: 'component_family'),
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
        Serializer\Groups(['read:family', 'write:family'])
    ]
    protected ?string $name = null;

    /**
     * @var null|self
     */
    #[
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    protected $parent;

    #[
        Assert\Length(exactly: 3),
        Assert\NotBlank,
        ORM\Column(type: 'char', length: 3, options: ['charset' => 'ascii']),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    private ?string $code = null;

    #[
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    private bool $copperable = false;

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getCopperable(): ?bool {
        return $this->copperable;
    }

    #[Serializer\Groups(['read:file'])]
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
