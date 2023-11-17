<?php

namespace App\Entity\Management\Society\Company;

use ApiPlatform\Core\Action\PlaceholderAction;
use App\Controller\Management\Company\BalanceSheetItemPatchController;
use App\Controller\Management\Company\BalanceSheetItemPostController;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\FileEntity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Entity\Traits\FileTrait;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use App\Filter\RelationFilter;
#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['balanceSheet']),
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'paymentRef'=>'partial',
        'stakeholder'=>'partial',
        'label'=>'partial',
        'paymentMethod'=>'partial',
        'subCategory'=>'partial',
        'paymentCategory'=>'exact'
    ]),
    ApiFilter(filterClass: OrderFilter::class, properties: [
        'paymentRef',
        'stakeholder',
        'label',
        'paymentMethod',
        'paymentCategory',
        'subCategory',
        'billDate' => 'DESC',
        'paymentDate' => 'DESC'
    ]),
    ApiFilter(filterClass: DateFilter::class, properties: [
        'billDate',
        'paymentDate'
    ]),
    ApiResource(
        collectionOperations: [
            'get' => [
                'method' => 'GET',
                'path' => '/balance-sheet-items',
                'openapi_context' => [
                    'description' => 'Récupère les écritures comptables',
                    'summary' => 'Récupère les écritures comptables'
                ]
            ],
            'post' => [
                'method' => 'POST',
                'path' => '/balance-sheet-items',
                'read' => true,
                'write' => true,
                'deserialize' => false, //OK
                'openapi_context' => [
                    'description' => 'Ajoute une écriture comptable',
                    'summary' => 'Ajoute une écriture comptable'
                ],
                'input_formats' => ['multipart'],
                'controller' => BalanceSheetItemPostController::class,
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une écriture comptable',
                    'summary' => 'Récupère une écriture comptable'
                ]
            ],
            'delete',
            'patch' => [
                'controller' => BalanceSheetItemPatchController::class,
                'input_formats' => ['multipart'],
                'method' => 'POST',
                'read' => true,
                'write' => true,
                'deserialize' => false, //OK
                'openapi_context' => [
                    'description' => 'Modifie un élément de bilan comptable',
                    'summary' => 'Modifie un élement de bilan comptable',
                ],
                'path' => '/balance-sheet-items/{id}',
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')',
                'status' => 200
            ]
            ],
        denormalizationContext: ['groups' => ['write:measure', 'write:file', 'write:balance-sheet-item']],
        normalizationContext: ['groups' => ['read:id', 'read:measure', 'read:file','read:balance-sheet-item'], 'skip_null_values' => false]
    ),
    ORM\Entity(),
    ORM\Table('balance_sheet_item')
 ]
class BalanceSheetItem extends Entity implements MeasuredInterface, FileEntity
{
    use FileTrait;
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
        ApiProperty(description: 'Lien pièce jointe'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:balance-sheet-item'])
    ]
    private ?string $url = null;
    #[
        ApiProperty(description: 'Lien image'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:balance-sheet-item'])
    ]
    protected ?string $filePath = null;
    #[
        ORM\ManyToOne(targetEntity: BalanceSheet::class),
        ORM\JoinColumn(nullable: false),
        ApiProperty(description: 'Bilan', example: '/api/balance-sheets/1'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private BalanceSheet $balanceSheet;
    #[
        ORM\Column(type: 'datetime', nullable: true),
        ApiProperty(description: 'Date de la facture', example: '2021-01-01'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private ?\DateTimeInterface $billDate;
    #[
        ORM\Column(type: 'datetime', nullable: true),
        ApiProperty(description: 'Date du paiement', example: '2021-01-01'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private ?\DateTimeInterface $paymentDate;
    #[
        ORM\Column(type: 'string', length: 255, nullable: true),
        ApiProperty(description: 'Référence du paiement', example: 'R565423'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private ?string $paymentRef;
    #[
        ORM\Column(type: 'string', length: 255, nullable: true),
        ApiProperty(description: 'Partie prenante', example: 'MANITOU'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private ?string $stakeholder;
    #[
        ORM\Column(type: 'string', length: 255, nullable: true),
        ApiProperty(description: 'Libellé', example: 'Cosse de batterie'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private ?string $label;
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
        ORM\Column(type: 'string', length: 255, nullable: true),
        ApiProperty(description: 'Méthode de paiement', example: 'ESPECES'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private ?string $paymentMethod;
    #[
        ORM\Column(type: 'string', length: 255, nullable: true),
        ApiProperty(description: 'Catégorie paiement', example: 'SALAIRE'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private ?string $paymentCategory;
    #[
        ORM\Column(type: 'string', length: 255, nullable: true),
        ApiProperty(description: 'Sous-catégorie', example: 'AVANCE SUR SALAIRE'),
        Serializer\Groups(['read:balance-sheet-item', 'write:balance-sheet-item'])
    ]
    private ?string $subCategory='';
    //endregion

    public function __construct() {
        $this->quantity = new Measure();
        $this->unitPrice = new Measure();
        $this->vat = new Measure();
        $this->amount = new Measure();
    }
    //region définition de getters et setters
    public function getBillDate(): ?\DateTimeInterface
    {
        return $this->billDate;
    }
    public function setBillDate(?\DateTimeInterface $billDate): BalanceSheetItem
    {
        $this->billDate = $billDate;
        return $this;
    }
    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }
    public function setPaymentDate(?\DateTimeInterface $paymentDate): BalanceSheetItem
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }
    public function getPaymentRef(): ?string
    {
        return $this->paymentRef;
    }
    public function setPaymentRef(?string $paymentRef): BalanceSheetItem
    {
        $this->paymentRef = $paymentRef;
        return $this;
    }
    public function getStakeholder(): ?string
    {
        return $this->stakeholder;
    }
    public function setStakeholder(?string $stakeholder): BalanceSheetItem
    {
        $this->stakeholder = $stakeholder;
        return $this;
    }
    public function getLabel(): ?string
    {
        return $this->label;
    }
    public function setLabel(?string $label): BalanceSheetItem
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
    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }
    public function setPaymentMethod(?string $paymentMethod): BalanceSheetItem
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }
    public function getPaymentCategory(): ?string
    {
        return $this->paymentCategory;
    }
    public function setPaymentCategory(?string $category): void
    {
        $this->paymentCategory = $category;
    }
    public function getSubCategory(): ?string
    {
        return $this->subCategory;
    }
    public function setSubCategory(?string $subCategory): BalanceSheetItem
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
    //region définition des méthodes associées à l'interface FileEntity
    #[
        ApiProperty(description: 'Icône', example: '/uploads/balance-sheet-items/1.jpg'),
        Serializer\Groups(['read:file'])
    ]
    final public function getFilepath(): ?string {
        return $this->filePath;
    }
    //endregion
    public function getBaseFolder() {
        return 'BalanceSheet/'.$this->balanceSheet->getId().'/BalanceSheetItems';
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): BalanceSheetItem
    {
        $this->url = $url;
        return $this;
    }
}