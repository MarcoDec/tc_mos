<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

trait CustomsCodeTrait {
    #[
        ApiProperty(description: 'Code douanier', example: '8544300089'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:customs-code', 'write:customs-code'])
    ]
    private ?string $customsCode = null;

    final public function getCustomsCode(): ?string {
        return $this->customsCode;
    }

    final public function setCustomsCode(?string $customsCode): self {
        $this->customsCode = $customsCode;
        return $this;
    }
}
