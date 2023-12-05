<?php

namespace App\Entity\Management\Society\Company;

use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Currency;
use App\Entity\Management\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Filter\RelationFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['company', 'currency']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['month' => 'exact', 'year' => 'exact']),
    ApiResource(
        collectionOperations: [
            'get' => [
                'path' => '/balance-sheets',
                'openapi_context' => [
                    'summary' => 'Obtenir la liste des bilans',
                    'description' => 'Retourne la liste des bilans de l\'entreprise'
                ]
            ],
            'post' => [
                'path' => '/balance-sheets',
                'denormalization_context' => [
                    'groups' => ['create:balance-sheet']
                ],
                'openapi_context' => [
                    'summary' => 'Créer un bilan',
                    'description' => 'Créer un bilan pour l\'entreprise'
                ]
            ]
        ],
        itemOperations: [
            'get',
            'patch',
            'delete'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:balance-sheet']
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:balance-sheet'],
            'skip_null_values' => false
        ],
    ),
    ORM\Entity(),
    ORM\Table('balance_sheet')
]
class BalanceSheet extends Entity implements MeasuredInterface
{
    #[
        ORM\Embedded,
        ApiProperty(description: 'Total des recettes',openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        Serializer\Groups(['read:balance-sheet', 'write:balance-sheet'])
    ]
    private Measure $totalIncome;
    #[
        ORM\Embedded,
        ApiProperty(description: 'Total des dépenses',openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        Serializer\Groups(['read:balance-sheet', 'write:balance-sheet'])
    ]
    private Measure $totalExpense;
    #[
        Assert\GreaterThanOrEqual(1),
        Assert\LessThanOrEqual(12),
        ORM\Column(type: 'integer'),
        Serializer\Groups(['create:balance-sheet', 'read:balance-sheet', 'write:balance-sheet'])
    ]
    private int $month;
    #[
        Assert\GreaterThanOrEqual(2021),
        ORM\Column(type: 'integer'),
        Serializer\Groups(['create:balance-sheet', 'read:balance-sheet', 'write:balance-sheet'])
    ]
    private int $year;

    #[
        ORM\ManyToOne(targetEntity: Currency::class, fetch: 'EAGER'),
        ORM\JoinColumn(nullable: true),
        ApiProperty(description: 'Devise', example: '/api/currencies/1'),
        Serializer\Groups(['create:balance-sheet', 'read:balance-sheet', 'write:balance-sheet'])
    ]
    private ?Currency $currency;

    #[
        ORM\ManyToOne(targetEntity: Company::class, fetch: 'EAGER'),
        ORM\JoinColumn(nullable: false),
        ApiProperty(description: 'Entreprise', example: '/api/companies/1'),
        Serializer\Groups(['create:balance-sheet', 'read:balance-sheet', 'write:balance-sheet'])
    ]
    private Company $company;

    #[
        ORM\ManyToOne(targetEntity: Unit::class),
        ORM\JoinColumn(nullable: true)
    ]
    private ?Unit $unit = null;

    #[ORM\OneToMany(mappedBy: 'balanceSheet', targetEntity: BalanceSheetItem::class, fetch: 'LAZY', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $balanceSheetItems;

    public function __construct() {
        $this->totalIncome = new Measure();
        $this->totalExpense = new Measure();
        $this->totalIncome->setValue(0);
        $this->totalExpense->setValue(0);
        $this->balanceSheetItems = new ArrayCollection();
        //        $this->unit = new Unit();
        //        $this->unit->setCode('U');
    }

    public function getTotalIncome(): Measure
    {
        return $this->totalIncome;
    }

    public function setTotalIncome(Measure $totalIncome): BalanceSheet
    {
        $this->totalIncome = $totalIncome;
        return $this;
    }

    public function getTotalExpense(): Measure
    {
        return $this->totalExpense;
    }

    public function setTotalExpense(Measure $totalExpense): BalanceSheet
    {
        $this->totalExpense = $totalExpense;
        return $this;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function setMonth(int $month): BalanceSheet
    {
        $this->month = $month;
        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): BalanceSheet
    {
        $this->year = $year;
        return $this;
    }

    public function getCompany(): Company
    {
        $this->currency = $this->company->getCurrency();
        return $this->company;
    }

    public function setCompany(Company $company): BalanceSheet
    {
        $this->company = $company;
        return $this;
    }

    public function getMeasures(): array
    {
        return [$this->totalIncome, $this->totalExpense];
    }
    public function getUnitMeasures(): array
    {
        return [];
    }
    public function getCurrencyMeasures(): array
    {
        return [$this->totalIncome, $this->totalExpense];
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): BalanceSheet
    {
        $this->currency = $currency;
        return $this;
    }

    public function getBalanceSheetItems(): Collection
    {
        return $this->balanceSheetItems;
    }

    public function setBalanceSheetItems(Collection $balanceSheetItems): BalanceSheet
    {
        $this->balanceSheetItems = $balanceSheetItems;
        return $this;
    }

    public function refreshIncomeAndExpense() {
        //dump('refreshIncomeAndExpense');
        $totalIncome = $this->calculateTotalIncome();
        $totalExpense = $this->calculateTotalExpense();
//        dump([
//            'totalIncome' => $totalIncome->getValue(),
//            'totalExpense' => $totalExpense->getValue()
//        ]);
        $this->setTotalIncome($totalIncome);
        $this->setTotalExpense($totalExpense);
    }
    private function calculateTotalExpense(): Measure
    {
        $totalExpense = new Measure();
        $totalExpense->setUnit($this->getCurrency());
        foreach ($this->getBalanceSheetItems() as $item) {
            if ($item->isExpense()) {
//                dump(['isExpense' => ['amount' => $item->getAmount(), 'vat' => $item->getVat()]]);
                $totalExpense->add($item->getAmount());
                $totalExpense->add($item->getVat());
            }
        }
        return $totalExpense;
    }
    private function calculateTotalIncome(): Measure
    {
        $totalIncome = new Measure();
        $totalIncome->setUnit($this->getCurrency());
        /** @var BalanceSheetItem $item */
        foreach ($this->getBalanceSheetItems() as $item) {
            if ($item->isIncome()) {
//                dump('isIncome', $item->getAmount(), $item->getVat());
                $totalIncome->add($item->getAmount());
                $totalIncome->add($item->getVat());
            } else {
                //dump('isNotIncome', $item->getAmount(), $item->getVat());
            }
        }
        return $totalIncome;
    }
}