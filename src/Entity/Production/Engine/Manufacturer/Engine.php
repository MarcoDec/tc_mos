<?php

namespace App\Entity\Production\Engine\Manufacturer;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Entity;
use App\Entity\Production\Engine\Engine as Equipment;
use App\Entity\Traits\RefTrait;
use DatetimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity,
    ORM\Table(name: 'manufacturer_engine')
]
class Engine extends Entity {
    use RefTrait;

    #[
        ApiProperty(description: 'Référence'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:ref', 'write:ref'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Date d\'arrivée', example: '2021-01-12'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ?DatetimeInterface $date = null;

    #[
        ORM\OneToOne(targetEntity: Equipment::class, inversedBy: 'manufacturerEngine', ),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private Equipment $engine;

    #[
        ApiProperty(description: 'Fabricant', readableLink: false, example: '/api/manufacturers/1'),
        ORM\ManyToOne(targetEntity: Manufacturer::class),
        Serializer\Groups(['read:manufacturer', 'write:manufacturer'])
    ]
    private ?Manufacturer $manufacturer = null;

    #[
        ApiProperty(description: 'Numéro de série', required: true, example: '54544244474432'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ?string $serialNumber = null;

    public function getDate(): ?DateTimeInterface {
        return $this->date;
    }

    public function getEngine(): Equipment {
        return $this->engine;
    }

    public function getManufacturer(): ?Manufacturer {
        return $this->manufacturer;
    }

    public function getSerialNumber(): ?string {
        return $this->serialNumber;
    }

    public function setDate(?DateTimeInterface $date): self {
        $this->date = $date;

        return $this;
    }

    public function setEngine(Equipment $engine): self {
        $this->engine = $engine;

        return $this;
    }

    public function setManufacturer(?Manufacturer $manufacturer): self {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function setSerialNumber(?string $serialNumber): self {
        $this->serialNumber = $serialNumber;

        return $this;
    }
}
