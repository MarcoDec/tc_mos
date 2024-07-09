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
            'groups' => ['write:component-customer', 'write:measure'],
            'openapi_definition_name' => 'CustomerComponent-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:component-customer'],
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
    //region properties
    /** @var Company */
    #[
        ApiProperty(description: 'Compagnie gérant la grille de prix', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'components'),
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private Company $administeredBy;
    #[
        ApiProperty(description: 'Référence', example: 'DH544G'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private ?string $code = null;

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
        ApiProperty(description: 'Poids cuivre', openapiContext: ['$ref' => '#/components/schemas/Measure-linear-density']),
        ORM\Embedded,
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private Measure $copperWeight;
    #[
        ApiProperty(description: 'Temps de livraison', openapiContext: ['$ref' => '#/components/schemas/Measure-duration']),
        ORM\Embedded,
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private Measure $deliveryTime;
    #[
        ApiProperty(description: 'Incoterms', readableLink: false, example: '/api/incoterms/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private ?Incoterms $incoterms = null;
    #[
        ApiProperty(description: 'Indice', example: '0'),
        ORM\Column(name: '`index`', options: ['default' => '0']),
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private string $index = '0';
    #[
        ApiProperty(description: 'Type de grille produit', example: KindType::TYPE_PROTOTYPE, openapiContext: ['enum' => KindType::TYPES]),
        Assert\Choice(choices: KindType::TYPES),
        ORM\Column(type: 'product_kind', options: ['default' => KindType::TYPE_SERIES]),
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private string $kind;

    #[
        ApiProperty(description: 'Proportion', example: '99'),
        ORM\Column(options: ['default' => 100, 'unsigned' => true]),
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private float $proportion = 100;
    #[
        ApiProperty(description: 'MOQ (Minimal Order Quantity)', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private Measure $moq;
    #[
        ApiProperty(description: 'Conditionnement', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private Measure $packaging;

    #[
        ApiProperty(description: 'Type de packaging', example: 'Palette'),
        ORM\Column(length: 30, nullable: true),
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private ?string $packagingKind = null;
    #[
        ApiProperty(description: 'Prix', example: '[]'),
        ORM\OneToMany(mappedBy: 'component', targetEntity: ComponentPrice::class, cascade: ['persist', 'remove'], fetch: 'EAGER'),
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private Collection $prices;

    //endregion
    public function __construct()
    {
        $this->prices = new ArrayCollection();
    }

    //region getters & setters
    final public function setAdministeredBy(Company $administeredBy): self
    {
        $this->administeredBy = $administeredBy;
        return $this;
    }

    /**
     * @return Company
     */
    final public function getAdministeredBy(): Company
    {
        return $this->administeredBy;
    }

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

    /**
     * @return string
     */
    public function getKind(): string
    {
        return $this->kind;
    }

    /**
     * @param string $kind
     * @return Component
     */
    public function setKind(string $kind): Component
    {
        $this->kind = $kind;
        return $this;
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getCopperWeight(): Measure
    {
        return $this->copperWeight;
    }

    public function setCopperWeight(Measure $copperWeight): void
    {
        $this->copperWeight = $copperWeight;
    }

    public function getDeliveryTime(): Measure
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(Measure $deliveryTime): void
    {
        $this->deliveryTime = $deliveryTime;
    }

    public function getIncoterms(): ?Incoterms
    {
        return $this->incoterms;
    }

    public function setIncoterms(?Incoterms $incoterms): void
    {
        $this->incoterms = $incoterms;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function setIndex(string $index): void
    {
        $this->index = $index;
    }

    public function getMoq(): Measure
    {
        return $this->moq;
    }

    public function setMoq(Measure $moq): void
    {
        $this->moq = $moq;
    }

    public function getPackaging(): Measure
    {
        return $this->packaging;
    }

    public function setPackaging(Measure $packaging): void
    {
        $this->packaging = $packaging;
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

    /**
     * @return float
     */
    public function getProportion(): float
    {
        return $this->proportion;
    }

    /**
     * @param float $proportion
     * @return Component
     */
    public function setProportion(float $proportion): Component
    {
        $this->proportion = $proportion;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPackagingKind(): ?string
    {
        return $this->packagingKind;
    }

    /**
     * @param string|null $packagingKind
     * @return Component
     */
    public function setPackagingKind(?string $packagingKind): Component
    {
        $this->packagingKind = $packagingKind;
        return $this;
    }

}
