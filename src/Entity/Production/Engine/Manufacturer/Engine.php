<?php

namespace App\Entity\Production\Engine\Manufacturer;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\EntityId;
use App\Entity\Production\Engine\Engine as Equipment;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Équipement : fiche fabricant',
        collectionOperations: [],
        itemOperations: [
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une fiche fabricant',
                    'summary' => 'Modifie une fiche fabricant',
                    'tags' => ['Engine']
                ]
            ]
        ],
        shortName: 'ManufacturerEngine',
        denormalizationContext: [
            'groups' => ['write:manufacturer-engine'],
            'openapi_definition_name' => 'ManufacturerEngine-write'
        ],
        normalizationContext: [
            'enable_max_depth' => true,
            'groups' => ['read:manufacturer-engine', 'read:id'],
            'openapi_definition_name' => 'ManufacturerEngine-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'manufacturer_engine')
]
class Engine extends EntityId {
    #[
        ApiProperty(description: 'Référence'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:manufacturer-engine', 'write:manufacturer-engine'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Date d\'arrivée', example: '2021-01-12'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:manufacturer-engine', 'write:manufacturer-engine'])
    ]
    private ?DateTimeImmutable $date = null;

    #[
        ApiProperty(description: 'Fabricant', readableLink: false, example: '/api/manufacturers/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:manufacturer-engine', 'write:manufacturer-engine'])
    ]
    private ?Manufacturer $manufacturer = null;

    #[
        ApiProperty(description: 'Numéro de série', example: '54544244474432'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:manufacturer-engine', 'write:manufacturer-engine'])
    ]
    private ?string $serialNumber = null;

    public function __construct(
        #[ORM\OneToOne(inversedBy: 'manufacturerEngine'),
        Serializer\Groups(['read:manufacturer-engine'])]
        private ?Equipment $engine = null
    ) {
    }

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getDate(): ?DateTimeImmutable {
        return $this->date;
    }

    final public function getEngine(): ?Equipment {
        return $this->engine;
    }

    final public function getManufacturer(): ?Manufacturer {
        return $this->manufacturer;
    }

    final public function getSerialNumber(): ?string {
        return $this->serialNumber;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setDate(?DateTimeImmutable $date): self {
        $this->date = $date;
        return $this;
    }

    final public function setEngine(?Equipment $engine): self {
        $this->engine = $engine;
        return $this;
    }

    final public function setManufacturer(?Manufacturer $manufacturer): self {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    final public function setSerialNumber(?string $serialNumber): self {
        $this->serialNumber = $serialNumber;
        return $this;
    }
}
