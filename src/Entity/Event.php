<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Management\Society\Company\Company;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class Event extends Entity {
    #[
        ApiProperty(description: 'Compagnie dirigeante', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:event', 'write:event'])
    ]
    protected ?Company $managingCompany = null;

    #[
        ApiProperty(description: 'Date'),
        ORM\Column(type: 'datetime_immutable'),
        Serializer\Groups(['read:event', 'write:event'])
    ]
    private ?DateTimeImmutable $date = null;

    #[
        ApiProperty(description: 'Fini', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:event', 'write:event'])
    ]
    private bool $done = false;

    #[
        ApiProperty(description: 'Nom', example: 'Congés d\'été'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:event', 'write:event'])
    ]
    private ?string $name = null;

    final public function getDate(): ?DateTimeImmutable {
        return $this->date;
    }

    final public function getManagingCompany(): ?Company {
        return $this->managingCompany;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function isDone(): bool {
        return $this->done;
    }

    final public function setDate(?DateTimeImmutable $date): self {
        $this->date = $date;
        return $this;
    }

    final public function setDone(bool $done): self {
        $this->done = $done;
        return $this;
    }

    final public function setManagingCompany(?Company $managingCompany): self {
        $this->managingCompany = $managingCompany;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}