<?php
namespace App\Entity\Production\Engine\Manufacturer;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Entity;
use App\Entity\Production\Engine\Attachment\ManufacturerEngineAttachment;
use App\Entity\Production\Engine\Engine as Equipment;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\RelationFilter;
use ApiPlatform\Core\Annotation\ApiFilter;

#[
    ApiFilter(OrderFilter::class, properties: ['code', 'manufacturer.name', 'name', 'partNumber']),
    ApiFilter(SearchFilter::class, properties: ['code' => 'partial', 'name' => 'partial', 'partNumber' => 'partial']),
    ApiFilter(RelationFilter::class, properties: ['manufacturer']),
    ApiResource(
        description: 'Équipement : fiche fabricant',
        collectionOperations: ['get', 'post'],
        itemOperations: [
            'get',
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une fiche fabricant',
                    'summary' => 'Modifie une fiche fabricant',
                    'tags' => ['ManufacturerEngine']
                ]
            ],
            'delete'
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
class Engine extends Entity {
    #[ORM\OneToMany(mappedBy: 'engine', targetEntity: ManufacturerEngineAttachment::class)]
    private Collection $attachments;

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
        ApiProperty(description: 'Fabricant', readableLink: true, example: '/api/manufacturers/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:manufacturer-engine', 'write:manufacturer-engine'])
    ]
    private ?Manufacturer $manufacturer = null;

    #[
        ApiProperty(description: 'Nom', example: 'Machine'),
        ORM\Column,
        Serializer\Groups(['read:manufacturer-engine', 'write:manufacturer-engine'])
    ]
    protected ?string $name = null;
    #[
        ApiProperty(description: 'Numéro d\'article', example: '54544244474432'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:manufacturer-engine', 'write:manufacturer-engine'])
    ]
    private ?string $partNumber = null;


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

    final public function getPartNumber(): ?string {
        return $this->partNumber;
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

    final public function setPartNumber(?string $partNumber): self {
        $this->partNumber = $partNumber;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    /**
     * @param Collection $attachments
     * @return Engine
     */
    public function setAttachments(Collection $attachments): Engine
    {
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Engine
     */
    public function setName(?string $name): Engine
    {
        $this->name = $name;
        return $this;
    }

}
