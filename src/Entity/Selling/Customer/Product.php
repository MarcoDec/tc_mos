<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Management\Unit;
use App\Entity\Project\Product\Product as TechnicalSheet;
use App\Filter\RelationFilter;
use App\Repository\Selling\Customer\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['customer', 'product']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['product.code' => 'partial', 'product.name' => 'partial', 'product.price.code' => 'partial', 'product.price.value' => 'partial',
        'product.index' => 'partial', 'product.forecastVolume.value' => 'partial', 'product.forecastVolume.code' => 'partial'
    ]),
    ApiResource(
        description: 'Produit Client',
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
            'get' => NO_ITEM_GET_OPERATION
        ],
        shortName: 'CustomerProduct',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:product-customer'],
            'openapi_definition_name' => 'CustomerProduct-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:product-customer'],
            'openapi_definition_name' => 'CustomerProduct-read',
            'skip_null_values' => false
        ],
        paginationEnabled: true
    ),
    ORM\Entity(repositoryClass: ProductRepository::class),
    ORM\Table(name: 'product_customer')
]
class Product extends Entity {
    /** @var Collection<int, Company> */
    #[
        ApiProperty(description: 'Compagnies dirigeantes', readableLink: false, example: ['/api/companies/1']),
        ORM\ManyToMany(targetEntity: Company::class, inversedBy: 'products'),
        Serializer\Groups(['read:product-customer'])
    ]
    private Collection $administeredBy;

    #[
        ApiProperty(description: 'Client', readableLink: true),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['read:product-customer', 'write:product-customer', 'read:manufacturing-order', 'read:expedition', 'read:nomenclature'])
    ]
    private ?Customer $customer;

    #[
        ApiProperty(description: 'Produit', readableLink: true),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['read:product-customer', 'write:product-customer', 'read:manufacturing-order','read:nomenclature'])
    ]
    private ?TechnicalSheet $product;

    public function __construct() {
        $this->administeredBy = new ArrayCollection();
    }

    final public function addAdministeredBy(Company $administeredBy): self {
        if (!$this->administeredBy->contains($administeredBy)) {
            $this->administeredBy->add($administeredBy);
            $administeredBy->addProduct($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Company>
     */
    final public function getAdministeredBy(): Collection {
        return $this->administeredBy;
    }

    final public function getCustomer(): ?Customer {
        return $this->customer;
    }

    final public function getProduct(): ?TechnicalSheet {
        return $this->product;
    }

    final public function getUnit(): ?Unit {
        return $this->product?->getUnit();
    }

    final public function removeAdministeredBy(Company $administeredBy): self {
        if ($this->administeredBy->contains($administeredBy)) {
            $this->administeredBy->removeElement($administeredBy);
            $administeredBy->removeProduct($this);
        }
        return $this;
    }

    final public function setCustomer(?Customer $customer): self {
        $this->customer = $customer;
        return $this;
    }

    final public function setProduct(?TechnicalSheet $product): self {
        $this->product = $product;
        return $this;
    }
}
