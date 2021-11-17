<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

trait CodeTrait {
    #[
        ApiProperty(description: 'Code', required: true),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:code', 'write:code'])
    ]
    protected ?string $code = null;

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }
}
