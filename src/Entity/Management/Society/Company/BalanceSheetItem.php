<?php

namespace App\Entity\Management\Society\Company;

use App\Entity\AbstractAttachment;
use App\Entity\Embeddable\Measure;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Entity\Traits\AttachmentTrait;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Annotation\ApiProperty;

#[
    ApiResource(
        collectionOperations: ['get', 'post'],
        itemOperations: ['get', 'patch', 'delete'],
        denormalizationContext: ['groups' => ['write:measure', 'write:balance-sheet-item']],
        normalizationContext: ['groups' => ['read:id', 'read:measure', 'read:balance-sheet-item'], 'skip_null_values' => false]
    ),
    ORM\Entity(),
    ORM\Table('balance_sheet_item')
 ]
class BalanceSheetItem extends AbstractAttachment implements MeasuredInterface
{
    use AttachmentTrait;
    //region définition des constantes
    public const INCOMES = [
        "VENTES"
    ];
    public const EXPENSES = [
        "DEPENSES NORMALES",
        "SALAIRES",
        "ACHATS MATIERES PREMIERES",
        "FRAIS DE TRANSPORT",
    ];
    public const CATEGORIES= [
        "VENTES",
        "DEPENSES NORMALES",
        "SALAIRES",
        "ACHATS MATIERES PREMIERES",
        "FRAIS DE TRANSPORT"
];
    public const SUB_CATEGORIES = [
        "SALAIRE" => [
            "SALAIRE DE BASE",
            "AVANCE SUR SALAIRE",
            "PRIME",
            "AVANTAGES EN NATURE",
            "CHARGES SOCIALES",
            "CHARGES PATRONALES",
            "CHARGES SALARIALES"
        ],
    ];
    public const PERIODICITES = [
        "AUCUNE",
        "PONCTUEL",
        "QUOTIDIEN",
        "HEBDOMADAIRE",
        "MENSUEL",
        "TRIMESTRIEL",
        "SEMESTRIEL",
        "ANNUEL"
    ];
    public const PAYMENT_METHODS = [
        "ESPECES",
        "CHEQUE",
        "VIREMENT",
        "PRELEVEMENT",
        "CARTE BANCAIRE",
        "OR",
        "AUTRE"
    ];
    //endregion
    //region définition des propriétés
    #[
        ORM\ManyToOne(targetEntity: BalanceSheet::class, inversedBy: 'balanceSheetItems'),
        ORM\JoinColumn(nullable: false),
        ApiProperty(description: 'Bilan', example: '/api/balance_sheets/1'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private BalanceSheet $balanceSheet;
    #[
        ORM\Column(type: 'datetime'),
        ApiProperty(description: 'Date de la facture', example: '2021-01-01'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private \DateTimeInterface $billDate;
    #[
        ORM\Column(type: 'datetime'),
        ApiProperty(description: 'Date du paiement', example: '2021-01-01'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private \DateTimeInterface $paymentDate;
    #[
        ORM\Column(type: 'string', length: 255),
        ApiProperty(description: 'Référence du paiement', example: 'R565423'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private string $paymentRef;
    #[
        ORM\Column(type: 'string', length: 255),
        ApiProperty(description: 'Partie prenante', example: 'MANITOU'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private string $stakeholder;
    #[
        ORM\Column(type: 'string', length: 255),
        ApiProperty(description: 'Libellé', example: 'Cosse de batterie'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private string $label;
    #[
        ORM\Embedded,
        ApiProperty(description: 'Quantité', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private Measure $quantity;
    #[
        ORM\Embedded,
        ApiProperty(description: 'Prix unitaire', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private Measure $unitPrice;
    #[
        ORM\Embedded,
        ApiProperty(description: 'TVA', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private Measure $vat;
    #[
        ORM\Embedded,
        ApiProperty(description: 'Montant', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private Measure $amount;
    #[
        ORM\Column(type: 'string', length: 255),
        ApiProperty(description: 'Méthode de paiement', example: 'ESPECES'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private string $paymentMethod;
    #[
        ORM\Column(type: 'string', length: 255),
        ApiProperty(description: 'Catégorie paiement', example: 'SALAIRE'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private string $paymentCategory;
    #[
        ORM\Column(type: 'string', length: 255),
        ApiProperty(description: 'Sous-catégorie', example: 'AVANCE SUR SALAIRE'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private string $subCategory;
    //endregion

    public function __construct() {
        parent::__construct();
        $this->quantity = new Measure();
        $this->unitPrice = new Measure();
        $this->vat = new Measure();
        $this->amount = new Measure();
    }
    //region définition de getters et setters
    public function getBillDate(): \DateTimeInterface
    {
        return $this->billDate;
    }
    public function setBillDate(\DateTimeInterface $billDate): BalanceSheetItem
    {
        $this->billDate = $billDate;
        return $this;
    }
    public function getPaymentDate(): \DateTimeInterface
    {
        return $this->paymentDate;
    }
    public function setPaymentDate(\DateTimeInterface $paymentDate): BalanceSheetItem
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }
    public function getPaymentRef(): string
    {
        return $this->paymentRef;
    }
    public function setPaymentRef(string $paymentRef): BalanceSheetItem
    {
        $this->paymentRef = $paymentRef;
        return $this;
    }
    public function getStakeholder(): string
    {
        return $this->stakeholder;
    }
    public function setStakeholder(string $stakeholder): BalanceSheetItem
    {
        $this->stakeholder = $stakeholder;
        return $this;
    }
    public function getLabel(): string
    {
        return $this->label;
    }
    public function setLabel(string $label): BalanceSheetItem
    {
        $this->label = $label;
        return $this;
    }
    public function getQuantity(): Measure
    {
        return $this->quantity;
    }
    public function setQuantity(Measure $quantity): BalanceSheetItem
    {
        $this->quantity = $quantity;
        return $this;
    }
    public function getUnitPrice(): Measure
    {
        return $this->unitPrice;
    }
    public function setUnitPrice(Measure $unitPrice): BalanceSheetItem
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }
    public function getVat(): Measure
    {
        return $this->vat;
    }
    public function setVat(Measure $vat): BalanceSheetItem
    {
        $this->vat = $vat;
        return $this;
    }
    public function getAmount(): Measure
    {
        return $this->amount;
    }
    public function setAmount(Measure $amount): BalanceSheetItem
    {
        $this->amount = $amount;
        return $this;
    }
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }
    public function setPaymentMethod(string $paymentMethod): BalanceSheetItem
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }
    public function getPaymentCategory(): string
    {
        return $this->paymentCategory;
    }
    public function setPaymentCategory(string $category): void
    {
        $this->paymentCategory = $category;
    }
    public function getSubCategory(): string
    {
        return $this->subCategory;
    }
    public function setSubCategory(string $subCategory): BalanceSheetItem
    {
        $this->subCategory = $subCategory;
        return $this;
    }
    public function getBalanceSheet(): BalanceSheet
    {
        return $this->balanceSheet;
    }
    public function setBalanceSheet(BalanceSheet $balanceSheet): BalanceSheetItem
    {
        $this->balanceSheet = $balanceSheet;
        return $this;
    }
    //endregion
    //region définition des méthodes associées à l'interface MeasuredInterface
    public function getMeasures(): array
    {
        return [$this->quantity, $this->unitPrice, $this->vat, $this->amount];
    }

    public function getUnit(): ?Unit
    {
        return null;
    }
    //endregion
    //region définition des méthodes associées à la classe AbstractAttachment
    public function getExpirationDirectoriesParameter(): string
    {
        return '';
    }

    public function getExpirationDurationParameter(): string
    {
        return '';
    }

    public function getExpirationDateStr(): string
    {
        return 'day';
    }

    public function getParameterClass(): string
    {
        return '';
    }

    //endregion
}