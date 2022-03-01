<?php

namespace App\Doctrine\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\Entity\Traits\NameTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class CurrentPlace {
    use NameTrait;

    public const TR_BLOCK = 'block';
    public const TR_DISABLE = 'disable';
    public const TR_PARTIALLY_UNLOCK = 'partially_unlock';
    public const TR_PARTIALLY_VALIDATE = 'partially_validate';
    public const TR_SUBMIT_VALIDATION = 'submit_validation';
    public const TR_UNLOCK = 'unlock';
    public const TR_VALIDATE = 'validate';

    #[
        ORM\Column(type: 'datetime_immutable', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP']),
        Serializer\Groups(['read:current-place'])
    ]
    protected ?DateTimeImmutable $date;

    #[
        ApiProperty(description: 'Nom', required: true),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:current-place'])
    ]
    protected ?string $name = null;

    public function __construct(?string $name = null) {
        $this->date = new DateTimeImmutable();
        $this->name = $name;
    }

    final public function __toString(): string {
        return $this->name ?? '';
    }

    abstract public function getTrafficLight(): int;

    abstract public function isDeletable(): bool;

    final public function getDate(): ?DateTimeImmutable {
        return $this->date;
    }

    final public function setName(?string $name): void {
        $this->name = $name;
        $this->date = new DateTimeImmutable();
    }
}
