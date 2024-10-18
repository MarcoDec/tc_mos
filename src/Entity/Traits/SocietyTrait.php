<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Embeddable\Measure;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

trait SocietyTrait {
    #[
        ApiProperty(description: 'Compte de comptabilité', required: false, example: 'D554DZ5'),
        Assert\Length(max: 50),
        ORM\Column(length: 50, nullable: true),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private ?string $accountingAccount = null;

    #[
        ApiProperty(description: 'Accusé de récéption', required: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private bool $ar = false;

    #[
        ApiProperty(description: 'Minimum de facturation', required: false, openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private Measure $invoiceMin;

    #[
        ApiProperty(description: 'Ordre minimum', required: false, openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private Measure $orderMin;

    #[
        ApiProperty(description: 'Taux ppm', required: false, example: '10'),
        Assert\NotNull(groups: ['Default', 'Society-create']),
        Assert\PositiveOrZero(groups: ['Default', 'Society-create']),
        ORM\Column(type: 'smallint', options: ['default' => 10, 'unsigned' => true]),
        Serializer\Groups(['create:society', 'read:society', 'write:society'])
    ]
    private int $ppmRate = 10;

    #[
        ApiProperty(description: 'TVA', required: false, example: 'FR'),
        Assert\Length(max: 255, groups: ['Default', 'Society-create']),
        ORM\Column(nullable: true),
        Serializer\Groups(['create:society', 'read:society', 'write:society'])
    ]
    private ?string $vat = null;

    public function __construct() {
        $this->invoiceMin = new Measure();
        $this->orderMin = new Measure();
    }

    final public function getAccountingAccount(): ?string {
        return $this->accountingAccount;
    }

    final public function getForceVat(): string {
        return $this->forceVat;
    }

    final public function getInvoiceMin(): Measure {
        return $this->invoiceMin;
    }

    final public function getOrderMin(): Measure {
        return $this->orderMin;
    }

    final public function getPpmRate(): int {
        return $this->ppmRate;
    }

    final public function getVat(): ?string {
        return $this->vat;
    }

    final public function isAr(): bool {
        return $this->ar;
    }

    final public function setAccountingAccount(?string $accountingAccount): self {
        $this->accountingAccount = $accountingAccount;
        return $this;
    }

    final public function setAr(bool $ar): self {
        $this->ar = $ar;
        return $this;
    }

    final public function setForceVat(string $forceVat): self {
        $this->forceVat = $forceVat;
        return $this;
    }

    final public function setInvoiceMin(Measure $invoiceMin): self {
        $this->invoiceMin = $invoiceMin;
        return $this;
    }

    final public function setOrderMin(Measure $orderMin): self {
        $this->orderMin = $orderMin;
        return $this;
    }

    final public function setPpmRate(int $ppmRate): self {
        $this->ppmRate = $ppmRate;
        return $this;
    }

    final public function setVat(?string $vat): self {
        $this->vat = $vat;
        return $this;
    }

}
