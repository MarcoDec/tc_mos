<?php

namespace App\Entity\Selling\Customer\Price;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Project\Product\KindType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Management\Unit;
use App\Entity\Project\Product\Product as TechnicalSheet;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Traits\Price\MainPriceTrait;
use App\Filter\RelationFilter;
use App\Repository\Selling\Customer\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['customer', 'product']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['product.code' => 'partial', 'product.name' => 'partial', 'product.price.code' => 'partial', 'product.price.value' => 'partial',
        'product.index' => 'partial', 'product.forecastVolume.value' => 'partial', 'product.forecastVolume.code' => 'partial'
    ]),
    ApiResource(
        description: 'Produit vu du Client',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les produits associés aux clients',
                    'summary' => 'Récupère les produits associés aux clients',
                ],
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Associe un produit à un client',
                    'summary' => 'Associe un produit à un client',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une association d\'un produit à un client',
                    'summary' => 'Supprime une association d\'un produit à un client',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_ADMIN.'\')'
            ],
            'get',
            'patch'
        ],
        shortName: 'CustomerProduct',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:product-customer', 'write:main-price'],
            'openapi_definition_name' => 'CustomerProduct-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:product-customer', 'read:main-price'],
            'openapi_definition_name' => 'CustomerProduct-read',
            'skip_null_values' => false
        ],
        paginationEnabled: true
    ),
    ORM\Entity(repositoryClass: ProductRepository::class),
    ORM\Table(name: 'product_customer')
]
class Product extends Entity {
    use MainPriceTrait;
    //region properties
    #[
        ApiProperty(description: 'Client', readableLink: true),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'productCustomers'),
        Serializer\Groups(['read:product-customer', 'write:product-customer', 'read:manufacturing-order', 'read:expedition', 'read:nomenclature'])
    ]
    private ?Customer $customer;
    #[
        ApiProperty(description: 'Produit', readableLink: true),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne(targetEntity: TechnicalSheet::class, inversedBy: 'productCustomers'),
        Serializer\Groups(['read:product-customer', 'write:product-customer', 'read:manufacturing-order','read:nomenclature'])
    ]
    private ?TechnicalSheet $product;

    #[
        ApiProperty(description: 'Prix', example: '[]'),
        ORM\OneToMany(mappedBy: 'product', targetEntity: ProductPrice::class, cascade: ['persist', 'remove']),
        Serializer\Groups(['read:product-customer', 'write:product-customer'])
    ]
    private Collection $prices;
    //endregion
    public function __construct() {
        $this->initialize();
        $this->prices = new ArrayCollection();
    }
    //region getters & setters
    final public function getCustomer(): ?Customer {
        return $this->customer;
    }

    final public function getProduct(): ?TechnicalSheet {
        return $this->product;
    }

    final public function getUnit(): ?Unit {
        return $this->product?->getUnit();
    }
    final public function setCustomer(?Customer $customer): self {
        $this->customer = $customer;
        return $this;
    }

    final public function setProduct(?TechnicalSheet $product): self {
        $this->product = $product;
        return $this;
    }

    public function getPrices(): Collection
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
    public function getBestPrice(Measure $quantity): ?ProductPrice {
        $bestPrice = new Measure();
        $bestPrice->setValue(0);
        $bestPrice->setCode('EUR');
        $possiblePrices = [];
        /** @var ProductPrice $price */
        foreach ($this->prices as $price) {
            if ($quantity->isGreaterThanOrEqual($price->getQuantity())) {
                $possiblePrices [] = $price;
            }
        }
        /** @var ProductPrice $price */
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
}
