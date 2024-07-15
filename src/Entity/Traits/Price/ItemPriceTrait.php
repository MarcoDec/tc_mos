<?php

namespace App\Entity\Traits\Price;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Embeddable\Measure;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use App\Validator as AppAssert;

trait ItemPriceTrait
{
    #[
        ApiProperty(description: 'Référence', example: 'DJZ54'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:price', 'write:price'])
    ]
    protected ?string $ref = null;
    #[
        ApiProperty(description: 'Prix', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:price', 'write:price'])
    ]
    private Measure $price;
    #[
        ApiProperty(description: 'Quantité', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        AppAssert\Measure,
        ORM\Embedded,
        Serializer\Groups(['read:price', 'write:price'])
    ]
    private Measure $quantity;

    public function initialize():void {
        $this->price = new Measure();
        $this->quantity = new Measure();
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(?string $ref): void
    {
        $this->ref = $ref;
    }

    public function getPrice(): Measure
    {
        return $this->price;
    }

    public function setPrice(Measure $price): void
    {
        $this->price = $price;
    }

    public function getQuantity(): Measure
    {
        return $this->quantity;
    }

    public function setQuantity(Measure $quantity): void
    {
        $this->quantity = $quantity;
    }

}