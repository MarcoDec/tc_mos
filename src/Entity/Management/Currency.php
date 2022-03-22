<?php

namespace App\Entity\Management;

use App\Repository\CurrencyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency extends AbstractUnit {
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    protected Collection $children;

    #[
        Assert\Length(exactly: 3),
        Assert\NotBlank,
        ORM\Column(type: 'char', length: 3, options: ['charset' => 'ascii'])
    ]
    protected ?string $code = null;

    protected ?string $name = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    protected $parent;

    #[ORM\Column(options: ['default' => false])]
    private bool $active = false;

    final public function getName(): ?string {
        return !empty($this->getCode()) ? Currencies::getName($this->getCode()) : null;
    }

    final public function getSymbol(): ?string {
        return !empty($this->getCode()) ? Currencies::getSymbol($this->getCode()) : null;
    }

    final public function isActive(): bool {
        return $this->active;
    }

    final public function setActive(bool $active): self {
        $this->active = $active;
        return $this;
    }
}
