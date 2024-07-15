<?php

namespace App\Entity\Traits\Price;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Project\Product\KindType;
use App\Entity\Embeddable\Measure;
use App\Entity\Logistics\Incoterms;
use App\Entity\Management\Society\Company\Company;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

trait MainPriceTrait
{
    /** @var ?Company */
    #[
        ApiProperty(description: 'Compagnies gérantes la grille de prix', readableLink: false, example: ['/api/companies/1']),
        ORM\ManyToOne(targetEntity: Company::class),
        Serializer\Groups(['read:main-price', 'write:main-price'])
    ]
    private ?Company $administeredBy;
    #[
        ApiProperty(description: 'Référence', example: 'DH544G'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:main-price', 'write:main-price'])
    ]
    private ?string $code = null;
    #[
        ApiProperty(description: 'Poids cuivre', openapiContext: ['$ref' => '#/components/schemas/Measure-linear-density']),
        ORM\Embedded,
        Serializer\Groups(['read:main-price', 'write:main-price'])
    ]
    private Measure $copperWeight;
    #[
        ApiProperty(description: 'Temps de livraison', openapiContext: ['$ref' => '#/components/schemas/Measure-duration']),
        ORM\Embedded,
        Serializer\Groups(['read:main-price', 'write:main-price'])
    ]
    private Measure $deliveryTime;
    #[
        ApiProperty(description: 'Incoterms', readableLink: false, example: '/api/incoterms/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:main-price', 'write:main-price'])
    ]
    private ?Incoterms $incoterms = null;
    #[
        ApiProperty(description: 'Indice', example: '0'),
        ORM\Column(name: '`index`', options: ['default' => '0']),
        Serializer\Groups(['read:main-price', 'write:main-price'])
    ]
    private string $index = '0';
    #[
        ApiProperty(description: 'Type de grille produit', example: KindType::TYPE_PROTOTYPE, openapiContext: ['enum' => KindType::TYPES]),
        ORM\Column(type: 'product_kind', options: ['default' => KindType::TYPE_SERIES]),
        Serializer\Groups(['read:main-price', 'write:main-price'])
    ]

    private string $kind;
    #[
        ApiProperty(description: 'MOQ (Minimal Order Quantity)', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:main-price', 'write:main-price'])
    ]
    private Measure $moq;
    #[
        ApiProperty(description: 'Conditionnement', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:main-price', 'write:main-price'])
    ]
    private Measure $packaging;
    #[
        ApiProperty(description: 'Type de packaging', example: 'Palette'),
        ORM\Column(length: 30, nullable: true),
        Serializer\Groups(['read:main-price', 'write:main-price'])
    ]
    private ?string $packagingKind = null;
    #[
        ApiProperty(description: 'Proportion', example: '99'),
        ORM\Column(options: ['default' => 100, 'unsigned' => true]),
        Serializer\Groups(['read:main-price', 'write:main-price'])
    ]
    private float $proportion = 100;

    public function initialize():void {
        $this->copperWeight = new Measure();
        $this->deliveryTime = new Measure();
        $this->moq = new Measure();
        $this->packaging = new Measure();
    }

    public function getAdministeredBy(): ?Company
    {
        return $this->administeredBy;
    }

    public function setAdministeredBy(Company $administeredBy): void
    {
        $this->administeredBy = $administeredBy;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getCopperWeight(): Measure
    {
        return $this->copperWeight;
    }

    public function setCopperWeight(Measure $copperWeight): void
    {
        $this->copperWeight = $copperWeight;
    }

    public function getDeliveryTime(): Measure
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(Measure $deliveryTime): void
    {
        $this->deliveryTime = $deliveryTime;
    }

    public function getIncoterms(): ?Incoterms
    {
        return $this->incoterms;
    }

    public function setIncoterms(?Incoterms $incoterms): void
    {
        $this->incoterms = $incoterms;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function setIndex(string $index): void
    {
        $this->index = $index;
    }

    public function getKind(): string
    {
        return $this->kind;
    }

    public function setKind(string $kind): void
    {
        $this->kind = $kind;
    }

    public function getMoq(): Measure
    {
        return $this->moq;
    }

    public function setMoq(Measure $moq): void
    {
        $this->moq = $moq;
    }

    public function getPackaging(): Measure
    {
        return $this->packaging;
    }

    public function setPackaging(Measure $packaging): void
    {
        $this->packaging = $packaging;
    }

    public function getPackagingKind(): ?string
    {
        return $this->packagingKind;
    }

    public function setPackagingKind(?string $packagingKind): void
    {
        $this->packagingKind = $packagingKind;
    }

    public function getProportion(): float
    {
        return $this->proportion;
    }

    public function setProportion(float $proportion): void
    {
        $this->proportion = $proportion;
    }

}