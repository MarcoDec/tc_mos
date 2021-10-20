<?php

namespace App\Entity\Traits;

trait CodeTrait
{
    /**
     * @Assert\Length(min=3, max=3)
     * @Assert\NotBlank
     *
     * @ORM\Column(length=3)
     *
     * @var string|null
     */
    private $code;

    final public function __toString(): string
    {
        return $this->getCode() ?? parent::__toString();
    }
    final public function getCode(): ?string {
        return $this->code;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }
}