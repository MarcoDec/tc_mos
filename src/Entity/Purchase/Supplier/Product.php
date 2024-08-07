<?php

namespace App\Entity\Purchase\Supplier;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Logistics\Incoterms;
use App\Entity\Management\Unit;
use App\Entity\Project\Product\Product as TechnicalSheet;
use App\Entity\Traits\Price\MainPriceTrait;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use App\Entity\Purchase\Supplier\Price\ProductPrice as SupplierProductPrice;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['product', 'supplier']),
    ApiResource(
        description: 'Produit Fournisseur',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les produits fournisseur',
                    'summary' => 'Récupère les produits fournisseur'
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un produit fournisseur',
                    'summary' => 'Créer un produit fournisseur'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un produit fournisseur',
                    'summary' => 'Supprime un produit fournisseur'
                ]
            ],
            'get',
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifier un produit fournisseur',
                    'summary' => 'Modifier un produit fournisseur',
                ]
            ],
        ],
        shortName: 'SupplierProduct',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:supplier-product', 'write:main-price'],
            'openapi_definition_name' => 'SupplierProduct-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:supplier-product', 'read:main-price'],
            'openapi_definition_name' => 'SupplierProduct-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity(),
    ORM\Table(name: 'supplier_product')
]
class Product extends Entity {
    use MainPriceTrait;
    #[
        ApiProperty(description: 'Produit', readableLink: false, example: '/api/product/1'),
        ORM\ManyToOne(targetEntity: TechnicalSheet::class, inversedBy: 'supplierProducts'),
        Serializer\Groups(['read:supplier-product', 'write:supplier-product'])
    ]
    private ?TechnicalSheet $product = null;
    /** @var DoctrineCollection<int, SupplierProductPrice> */
    #[
        ORM\OneToMany(mappedBy: 'product', targetEntity: SupplierProductPrice::class, fetch: 'EAGER'),
        Serializer\Groups(['read:supplier-product'])
    ]
    private DoctrineCollection $prices;

    #[
        ApiProperty(description: 'Fournisseur', readableLink: false, example: '/api/suppliers/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:supplier-product', 'write:supplier-product'])
    ]
    private ?Supplier $supplier = null;

    public function __construct() {
        $this->initialize();
        $this->prices = new ArrayCollection();
    }

    #[
        ApiProperty(description: 'Meilleur prix'),
        Serializer\Groups(['read:id', 'read:supplier-product'])
    ]
    final public function getBestPrice():Measure {
        $bestPrice=new Measure();
        //On récupère tous les prix
        $prices = $this->getPrices();
        //dump(['prices'=>$prices]);
        if (count($prices)>0) {
            /** @var SupplierProductPrice $supplierProductPrice */
            $filteredPrices = $prices
            ->filter(function($supplierProductPrice){ // On retire tous les enregistrements qui ont une quantité à zéro ou un prix à zéro
                $quantity = $supplierProductPrice->getQuantity()->getValue();
                $price = $supplierProductPrice->getPrice()->getValue();
                return $price >0;
            })->toArray();
            usort($filteredPrices, function( $a,  $b){
                    return $b->getPrice()->getValue() < $a->getPrice()->getValue();
                });
            $bestPrice = $filteredPrices[0]->getPrice();
        }
        return $bestPrice;
    }

    final public function getProduct(): ?TechnicalSheet {
        return $this->product;
    }

    public function getPrices()
    {
        return $this->prices->filter(function ($price) {
            return $price->isDeleted() === false;
        });
    }

    final public function getSupplier(): ?Supplier {
        return $this->supplier;
    }

    final public function getUnit(): ?Unit {
        return $this->product?->getUnit();
    }

    final public function setProduct(?TechnicalSheet $product): self {
        $this->product = $product;
        return $this;
    }

    final public function setSupplier(?Supplier $supplier): self {
        $this->supplier = $supplier;
        return $this;
    }

}