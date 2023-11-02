<?php

namespace App\Entity\Management\Society\Company;

use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
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
class BalanceSheetItem extends Entity implements MeasuredInterface
{
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
        ApiProperty(description: 'Catégorie', example: 'SALAIRE'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private string $category;
    #[
        ORM\Column(type: 'string', length: 255),
        ApiProperty(description: 'Sous-catégorie', example: 'AVANCE SUR SALAIRE'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private string $subCategory;
    //endregion

    public function __construct() {
        $this->quantity = new Measure();
        $this->unitPrice = new Measure();
        $this->vat = new Measure();
        $this->amount = new Measure();
    }

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
    public function getCategory(): string
    {
        return $this->category;
    }
    public function setCategory(string $category): BalanceSheetItem
    {
        $this->category = $category;
        return $this;
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

    public function getMeasures(): array
    {
        return [$this->quantity, $this->unitPrice, $this->vat, $this->amount];
    }

    public function getUnit(): ?Unit
    {
        return null;
    }
}