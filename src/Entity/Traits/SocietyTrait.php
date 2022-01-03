<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Logistics\Incoterms;
use App\Entity\Management\InvoiceTimeDue;
use App\Entity\Management\VatMessage;
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
    private ?string $accountingAccount;

    #[
        ApiProperty(description: 'Accusé de récéption', required: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private ?bool $ar = false;

    #[
        ApiProperty(description: 'Forcer la TVA', required: true, example: 0),
        ORM\Column(options: ['default' => VatMessage::FORCE_DEFAULT, 'unsigned' => true], type: 'smallint'),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private int $forceVat = VatMessage::FORCE_DEFAULT;

    #[
        ApiProperty(description: 'Incoterms', required: false),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Incoterms::class),
        Serializer\Groups(['read:incoterms', 'write:incoterms'])
    ]
    private ?Incoterms $incoterms;

    #[
        ApiProperty(description: 'Minimum de facturation', required: false, example: '2'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'float'),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private ?float $invoiceMin = 0;

    #[
        ApiProperty(description: 'Délai de paiement des facture', required: false),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: InvoiceTimeDue::class),
        Serializer\Groups(['read:invoice-time-due', 'write:invoice-time-due'])
    ]
    private ?InvoiceTimeDue $invoiceTimeDue;

    #[
        ApiProperty(description: 'Ordre minimum', required: false, example: '5'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'float'),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private ?float $orderMin = 0;

    #[
        ApiProperty(description: 'Taux ppm', required: false, example: '10'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 10, 'unsigned' => true], type: 'smallint'),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private ?int $ppmRate = 10;

    #[
        ApiProperty(description: 'TVA', required: false, example: 'FR'),
        Assert\Length(max: 255),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private ?string $vat = null;

    #[
        ApiProperty(description: 'Message TVA', required: false),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: VatMessage::class),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    private ?VatMessage $vatMessage;

    final public function getAccountingAccount(): ?string {
        return $this->accountingAccount;
    }

    final public function getAr(): ?bool {
        return $this->ar;
    }

    final public function getForceVat(): int {
        return $this->forceVat;
    }

    final public function getIncoterms(): ?Incoterms {
        return $this->incoterms;
    }

    final public function getInvoiceMin(): ?float {
        return $this->invoiceMin;
    }

    final public function getInvoiceTimeDue(): ?InvoiceTimeDue {
        return $this->invoiceTimeDue;
    }

    final public function getOrderMin(): ?float {
        return $this->orderMin;
    }

    final public function getPpmRate(): ?int {
        return $this->ppmRate;
    }

    final public function getVat(): ?string {
        return $this->vat;
    }

    final public function getVatMessage(): ?VatMessage {
        return $this->vatMessage;
    }

    final public function setAccountingAccount(?string $accountingAccount): self {
        $this->accountingAccount = $accountingAccount;
        return $this;
    }

    final public function setAr(?bool $ar): self {
        $this->ar = $ar;
        return $this;
    }

    final public function setForceVat(int $forceVat): self {
        $this->forceVat = $forceVat;
        return $this;
    }

    final public function setIncoterms(?Incoterms $incoterms): self {
        $this->incoterms = $incoterms;
        return $this;
    }

    final public function setInvoiceMin(?float $invoiceMin): self {
        $this->invoiceMin = $invoiceMin;
        return $this;
    }

    final public function setInvoiceTimeDue(?InvoiceTimeDue $invoiceTimeDue): self {
        $this->invoiceTimeDue = $invoiceTimeDue;
        return $this;
    }

    final public function setOrderMin(?float $orderMin): self {
        $this->orderMin = $orderMin;
        return $this;
    }

    final public function setPpmRate(?int $ppmRate): self {
        $this->ppmRate = $ppmRate;
        return $this;
    }

    final public function setVat(?string $vat): self {
        $this->vat = $vat;
        return $this;
    }

    final public function setVatMessage(?VatMessage $vatMessage): self {
        $this->vatMessage = $vatMessage;
        return $this;
    }
}
