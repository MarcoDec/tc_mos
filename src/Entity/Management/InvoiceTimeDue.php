<?php

namespace App\Entity\Management;

use App\Entity\Entity;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity,
    UniqueEntity(['days', 'daysAfterEndOfMonth', 'endOfMonth']),
    UniqueEntity('name')
]
class InvoiceTimeDue extends Entity {
    #[
        Assert\Length(min: 0, max: 31),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true])
    ]
    private ?int $days = 0;

    #[
        Assert\Length(min: 0, max: 31),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true])
    ]
    private ?int $daysAfterEndOfMonth = 0;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $endOfMonth = false;

    #[
        ORM\Column(length: 30),
        Assert\Length(min: 3, max: 30),
        Assert\NotBlank
    ]
    private ?string $name = null;

    final public function getDays(): ?int {
        return $this->days;
    }

    final public function getDaysAfterEndOfMonth(): ?int {
        return $this->daysAfterEndOfMonth;
    }

    final public function getEndOfMonth(): ?bool {
        return $this->endOfMonth;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setDays(?int $days): self {
        $this->days = $days;
        return $this;
    }

    final public function setDaysAfterEndOfMonth(?int $daysAfterEndOfMonth): self {
        $this->daysAfterEndOfMonth = $daysAfterEndOfMonth;
        return $this;
    }

    final public function setEndOfMonth(?bool $endOfMonth): self {
        $this->endOfMonth = $endOfMonth;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
