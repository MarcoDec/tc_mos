<?php

namespace App\Entity\Management\Society\Company;

use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Validator as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        collectionOperations: [
            'get' => [
                'security' => "is_granted('ROLE_ADMIN')",
                'path' => '/balance_sheets',
                'openapi_context' => [
                    'summary' => 'Obtenir la liste des bilans',
                    'description' => 'Retourne la liste des bilans de l\'entreprise',
                    'parameters' => [
                        [
                            'in' => 'query',
                            'name' => 'year',
                            'schema' => [
                                'type' => 'integer',
                                'example' => 2021
                            ],
                            'description' => 'Année du bilan'
                        ],
                        [
                            'in' => 'query',
                            'name' => 'month',
                            'schema' => [
                                'type' => 'integer',
                                'example' => 1
                            ],
                            'description' => 'Mois du bilan'
                        ]
                    ]
                ]
            ],
            'post' => [
                'security' => "is_granted('ROLE_ADMIN')",
                'path' => '/balance_sheets',
                'openapi_context' => [
                    'summary' => 'Créer un bilan',
                    'description' => 'Créer un bilan pour l\'entreprise',
                    'requestBody' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/BalanceSheet'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        itemOperations: [
            'get',
            'patch'
        ]
    ),
    ORM\Entity('balance_sheet'),
    ORM\Table('balance_sheet')
]
class BalanceSheet extends Entity implements MeasuredInterface
{
    #[
        AppAssert\Measure,
        ORM\Embedded,
    ]
    private Measure $totalIncome;
    #[
        AppAssert\Measure,
        ORM\Embedded,
    ]
    private Measure $totalExpense;
    #[
        Assert\GreaterThanOrEqual(1),
        Assert\LessThanOrEqual(12),
        ORM\Column(type: 'integer')
    ]
    private int $month;
    #[
        Assert\GreaterThanOrEqual(2021),
        ORM\Column(type: 'integer')
    ]
    private int $year;

    #[
        ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'balanceSheets'),
        ORM\JoinColumn(nullable: false)
    ]
    private Company $company;

    public function __construct() {
        $this->totalIncome = new Measure();
        $this->totalExpense = new Measure();
        $this->totalIncome->setValue(0);
        $this->totalExpense->setValue(0);
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

    public function getUnit(): ?Unit
    {
        return $this->company->getUnit();
    }
}