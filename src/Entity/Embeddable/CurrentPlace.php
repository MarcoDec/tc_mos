<?php

namespace App\Entity\Embeddable;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\MappedSuperclass]
abstract class CurrentPlace implements Stringable {
    final public const TR_ACCEPT = 'accept';
    final public const TR_BLOCK = 'block';
    final public const TR_DISABLE = 'disable';
    final public const TR_PARTIALLY_UNLOCK = 'partially_unlock';
    final public const TR_PARTIALLY_VALIDATE = 'partially_validate';
    final public const TR_REJECT = 'reject';
    final public const TR_SUBMIT_VALIDATION = 'submit_validation';
    final public const TR_SUPERVISE = 'supervise';
    final public const TR_UNLOCK = 'unlock';
    final public const TR_VALIDATE = 'validate';

    #[
        ORM\Column(type: 'datetime_immutable', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP']),
        Serializer\Groups(['read:current-place'])
    ]
    protected DateTimeImmutable $date;

    public function __construct(protected ?string $name = null) {
        $this->date = new DateTimeImmutable();
    }

    final public function __toString(): string {
        return $this->name ?? '';
    }

    abstract public function getTrafficLight(): int;

    abstract public function isDeletable(): bool;

    abstract public function isFrozen(): bool;

    final public function getDate(): DateTimeImmutable {
        return $this->date;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setName(?string $name): void {
        $this->name = $name;
        $this->date = new DateTimeImmutable();
    }
}
