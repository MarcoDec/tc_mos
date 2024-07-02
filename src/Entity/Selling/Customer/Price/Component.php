<?php

namespace App\Entity\Selling\Customer\Price;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Project\Product\KindType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Logistics\Incoterms;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Management\Unit;
use App\Entity\Purchase\Component\Component as TechnicalSheet;
use App\Entity\Selling\Customer\Customer;
use App\Filter\RelationFilter;
use App\Repository\Selling\Customer\ComponentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
            'groups' => ['write:component-customer'],
            'openapi_definition_name' => 'CustomerComponent-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:component-customer'],
            'openapi_definition_name' => 'CustomerComponent-read',
            'skip_null_values' => false
        ],
        paginationEnabled: true
    ),
    ORM\Entity(repositoryClass: ComponentRepository::class),
    ORM\Table(name: 'component_customer')
]
class Component extends Entity
{
    //region properties
    /** @var Collection<int, Company> */
    #[
        ApiProperty(description: 'Compagnies gérantes la grille de prix', readableLink: false, example: ['/api/companies/1']),
        ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'components'),
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private Collection $administeredBy;
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
        Serializer\Groups(['read:product-customer', 'write:product-customer'])
    ]

    private KindType $kind;
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
        ApiProperty(description: 'Prix', readableLink: false, example: '/api/customer-component-prices/1'),
        ORM\OneToMany(mappedBy: 'component', targetEntity: ComponentPrice::class, cascade: ['persist', 'remove']),
        Serializer\Groups(['read:component-customer', 'write:component-customer'])
    ]
    private Collection $componentPrices;

    //endregion
    public function __construct()
    {
        $this->administeredBy = new ArrayCollection();
        $this->componentPrices = new ArrayCollection();
    }

    //region getters & setters
    final public function addAdministeredBy(Company $administeredBy): self
    {
        if (!$this->administeredBy->contains($administeredBy)) {
            $this->administeredBy->add($administeredBy);
            $administeredBy->addProduct($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Company>
     */
    final public function getAdministeredBy(): Collection
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

    final public function removeAdministeredBy(Company $administeredBy): self
    {
        if ($this->administeredBy->contains($administeredBy)) {
            $this->administeredBy->removeElement($administeredBy);
            $administeredBy->removeProduct($this);
        }
        return $this;
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

    public function getComponentPrices(): Collection
    {
        return $this->componentPrices;
    }

    public function setComponentPrices(Collection $componentPrices): void
    {
        $this->componentPrices = $componentPrices;
    }

    /**
     * @return KindType
     */
    public function getKind(): KindType
    {
        return $this->kind;
    }

    /**
     * @param KindType $kind
     * @return Component
     */
    public function setKind(KindType $kind): Component
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
        foreach ($this->componentPrices as $price) {
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
}
