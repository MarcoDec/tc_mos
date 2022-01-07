<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Management\Society\Company;
use App\Entity\Traits\NameTrait;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class Event extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Congés d\'été'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Date', example: '2021-01-12 10:39:37'),
        Assert\DateTime,
        ORM\Column(type: 'datetime', nullable: true),
        Serializer\Groups(['read:event_date', 'write:event_date'])
    ]
    private ?DateTimeInterface $date;

    #[
        ApiProperty(description: 'Fini ?', required: true, example: false),
        ORM\Column(type: 'boolean', options: ['default' => false]),
        Serializer\Groups(['read:event', 'write:event'])
    ]
    private bool $done = false;

    #[
        ApiProperty(description: 'Compagnie dirigeante', readableLink: false, example: '/api/companies/2'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Company::class),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private ?Company $managingCompany;

    final public function getDate(): ?DateTimeInterface {
        return $this->date;
    }

    final public function getManagingCompany(): ?Company {
        return $this->managingCompany;
    }

    final public function isDone(): bool {
        return $this->done;
    }

    final public function setDate(?DateTimeInterface $date): self {
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
}
