<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Management\VatMessageForce;
use App\Entity\Embeddable\Measure;
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
    private ?string $accountingAccount = null;

    #[
        ApiProperty(description: 'Accusé de récéption', required: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private bool $ar = false;

    #[
        ApiProperty(description: 'Forcer la TVA', required: true, example: VatMessageForce::TYPE_FORCE_DEFAULT, openapiContext: ['enum' => VatMessageForce::TYPES]),
        Assert\Choice(choices: VatMessageForce::TYPES),
        ORM\Column(type: 'vat_message_force', options: ['default' => VatMessageForce::TYPE_FORCE_DEFAULT]),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private string $forceVat = VatMessageForce::TYPE_FORCE_DEFAULT;

    #[
        ApiProperty(description: 'Incoterms', readableLink: false, required: false, example: '/api/incoterms/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:society', 'create:society', 'write:society'])
    ]
    private ?Incoterms $incoterms = null;

    #[
        ApiProperty(description: 'Minimum de facturation', required: false, openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private Measure $invoiceMin;

    #[
        ApiProperty(description: 'Délai de paiement des facture', required: false, example: '/api/invoice-time-dues/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private ?InvoiceTimeDue $invoiceTimeDue = null;

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

    #[
        ApiProperty(description: 'Message TVA', readableLink:false, required: false, example: '/api/vat-messages/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private ?VatMessage $vatMessage = null;

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

    final public function getIncoterms(): ?Incoterms {
        return $this->incoterms;
    }

    final public function getInvoiceMin(): Measure {
        return $this->invoiceMin;
    }

    final public function getInvoiceTimeDue(): ?InvoiceTimeDue {
        return $this->invoiceTimeDue;
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

    final public function getVatMessage(): ?VatMessage {
        return $this->vatMessage;
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

    final public function setIncoterms(?Incoterms $incoterms): self {
        $this->incoterms = $incoterms;
        return $this;
    }

    final public function setInvoiceMin(Measure $invoiceMin): self {
        $this->invoiceMin = $invoiceMin;
        return $this;
    }

    final public function setInvoiceTimeDue(?InvoiceTimeDue $invoiceTimeDue): self {
        $this->invoiceTimeDue = $invoiceTimeDue;
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

    final public function setVatMessage(?VatMessage $vatMessage): self {
        $this->vatMessage = $vatMessage;
        return $this;
    }
}
