<?php

namespace App\Entity\Hr;

use App\Entity\Entity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class TimeSlot extends Entity {
    /**
     * @ORM\Column(type="time_immutable")
     */
    #[
        ORM\Column(type: 'time_immutable'),
        Serializer\Context([DateTimeNormalizer::FORMAT_KEY => 'H:i:s']),
        Serializer\Groups(['read:time-slot', 'write:time-slot'])
    ]
    private ?DateTimeImmutable $end = null;

    #[
        ORM\Column(type: 'time_immutable', nullable: true),
        Serializer\Context([DateTimeNormalizer::FORMAT_KEY => 'H:i:s']),
        Serializer\Groups(['read:time-slot', 'write:time-slot'])
    ]
    private ?DateTimeImmutable $endBreak = null;

    #[
        Assert\NotBlank,
        ORM\Column(length: 10),
        Serializer\Groups(['read:time-slot', 'write:time-slot'])
    ]
    private ?string $name = null;

    #[
        ORM\Column(type: 'time_immutable'),
        Serializer\Context([DateTimeNormalizer::FORMAT_KEY => 'H:i:s']),
        Serializer\Groups(['read:time-slot', 'write:time-slot'])
    ]
    private ?DateTimeImmutable $start = null;

    #[
        ORM\Column(type: 'time_immutable', nullable: true),
        Serializer\Context([DateTimeNormalizer::FORMAT_KEY => 'H:i:s']),
        Serializer\Groups(['read:time-slot', 'write:time-slot'])
    ]
    private ?DateTimeImmutable $startBreak = null;

    final public function getEnd(): ?DateTimeImmutable {
        return $this->end;
    }

    final public function getEndBreak(): ?DateTimeImmutable {
        return $this->endBreak;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getStart(): ?DateTimeImmutable {
        return $this->start;
    }

    final public function getStartBreak(): ?DateTimeImmutable {
        return $this->startBreak;
    }

    final public function setEnd(?DateTimeImmutable $end): self {
        $this->end = $end;
        return $this;
    }

    final public function setEndBreak(?DateTimeImmutable $endBreak): self {
        $this->endBreak = $endBreak;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setStart(?DateTimeImmutable $start): self {
        $this->start = $start;
        return $this;
    }

    final public function setStartBreak(?DateTimeImmutable $startBreak): self {
        $this->startBreak = $startBreak;
        return $this;
    }
}
