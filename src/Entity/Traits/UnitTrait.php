<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Management\Unit;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

trait UnitTrait {
    #[
        ApiProperty(description: 'Unité', readableLink: false, example: '/api/units/1'),
        ORM\ManyToOne(targetEntity: Unit::class),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    private $unit;

    final public function getUnit(): ?Unit {
        return $this->unit;
    }

    final public function setUnit(?Unit $unit): self {
        $this->unit = $unit;
        return $this;
    }
}
