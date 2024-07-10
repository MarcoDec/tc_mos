<?php

namespace App\Entity\Selling\Customer\Price;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Project\Product\KindType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Logistics\Incoterms;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Management\Unit;
use App\Entity\Purchase\Component\Component as TechnicalSheet;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Traits\Price\MainPriceTrait;
use App\Filter\RelationFilter;
use App\Repository\Selling\Customer\ComponentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['customer', 'component', 'kind']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['component.code' => 'partial', 'component.name' => 'partial', 'component.price.code' => 'partial', 'component.price.value' => 'partial',
        'component.index' => 'partial', 'component.forecastVolume.value' => 'partial', 'component.forecastVolume.code' => 'partial'
    ]),
    ApiResource(
        description: 'Composant Client',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les composants associés aux clients',
                    'summary' => 'Récupère les composants associés aux clients',
                ],
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Associe un composant à un client',
                    'summary' => 'Associe un composant à un client',
                ],
                'security' => 'is_granted(\'' . Roles::ROLE_SELLING_WRITER . '\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une association d\'un composant à un client',
                    'summary' => 'Supprime une association d\'un composant à un client',
                ],
                'security' => 'is_granted(\'' . Roles::ROLE_SELLING_ADMIN . '\')'
            ],
            'get',
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une association d\'un composant à un client',
                    'summary' => 'Modifie une association d\'un composant à un client',
                ],
                'security' => 'is_granted(\'' . Roles::ROLE_SELLING_WRITER . '\')'
            ]
        ],
        shortName: 'CustomerComponent',
        attributes: [
            'security' => 'is_granted(\'' . Roles::ROLE_SELLING_READER . '\')'
        ],
        denormalizationContext: [
            'groups' => ['write:component-customer', 'write:measure', 'write:main-price'],
            'openapi_definition_name' => 'CustomerComponent-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:component-customer', 'read:main-price'],
            'openapi_definition_name' => 'CustomerComponent-read',
            'skip_null_values' => false
        ],
        paginationEnabled: true
    ),
    ORM\Entity(repositoryClass: ComponentRepository::class),
    ORM\Table(name: 'component_customer')
]
class Component extends Entity implements MeasuredInterface
{
    use MainPriceTrait;

    //region properties
    #[
        ApiProperty(description: 'Client', readableLink: true),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'componentCustomers'),
        Serializer\Groups(['read:component-customer', 'write:component-customer', 'read:manufacturing-order', 'read:expedition', 'read:nomenclature'])
    ]
    private ?Customer $customer;

    #[
        ApiProperty(description: 'Composant', readableLink: true),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne(targetEntity: TechnicalSheet::class, inversedBy: 'customerComponents'),
        Serializer\Groups(['read:component-customer', 'write:component-customer', 'read:manufacturing-order', 'read:nomenclature'])
    ]
    private ?TechnicalSheet $component;

    #[
        ApiProperty(description: 'Prix', example: '[]'),
        ORM\OneToMany(mappedBy: 'component', targetEntity: ComponentPrice::class, cascade: ['persist', 'remove'], fetch: 'EAGER'),
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private Collection $prices;

    //endregion
    public function __construct()
    {
        $this->initialize();
        $this->prices = new ArrayCollection();
    }

    //region getters & setters
    final public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    final public function getComponent(): ?TechnicalSheet
    {
        return $this->component;
    }

    final public function getUnit(): ?Unit
    {
        return $this->component?->getUnit();
    }

    final public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    final public function setComponent(?TechnicalSheet $component): self
    {
        $this->component = $component;
        return $this;
    }

    public function getPrices(): ArrayCollection|DoctrineCollection
    {
        return $this->prices->filter(function ($price) {
            return $price->isDeleted() === false;
        });
    }

    public function setPrices(Collection $prices): void
    {
        $this->prices = $prices;
    }

    // On récupère le meilleur prix associé au produit en fonction de la quantité passée en paramètre
    public function getBestPrice(Measure $quantity): ?ComponentPrice
    {
        $bestPrice = new Measure();
        $bestPrice->setValue(0);
        $bestPrice->setCode('EUR');
        $possiblePrices = [];
        /** @var ComponentPrice $price */
        foreach ($this->prices as $price) {
            if ($quantity->isGreaterThanOrEqual($price->getQuantity())) {
                $possiblePrices [] = $price;
            }
        }
        /** @var ComponentPrice $price */
        foreach ($possiblePrices as $price) {
            // Si le prix à une valeur supérieure à zéro et que le meilleur prix est à zéro alors on le prend
            if ($price->getPrice()->getValue() > 0 && $bestPrice->getValue() === 0) {
                $bestPrice = $price->getPrice();
            } else {
                // Si le prix est inférieur au meilleur prix alors on le prend
                if ($price->getPrice()->isGreaterThanOrEqual($bestPrice->getValue())) {
                    $bestPrice = $price->getPrice();
                }
            }
        }
        return $bestPrice;
    }
    //endregion

    /**
     * @return Measure[]
     */
    public function getMeasures(): array
    {
        return [
            $this->copperWeight,
            $this->deliveryTime,
            $this->moq,
            $this->packaging
        ];
    }

    /**
     * @return Measure[]
     */
    public function getUnitMeasures(): array
    {
        return [
            $this->copperWeight,
            $this->deliveryTime,
            $this->moq,
            $this->packaging
        ];
    }

    /**
     * @return Measure[]
     */
    public function getCurrencyMeasures(): array
    {
        return [];
    }
}
