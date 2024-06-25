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
            'groups' => ['write:measure', 'write:supplier-product'],
            'openapi_definition_name' => 'SupplierProduct-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:supplier-product'],
            'openapi_definition_name' => 'SupplierProduct-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity(),
    ORM\Table(name: 'supplier_product')
]
class Product extends Entity {
    #[
        ApiProperty(description: 'Référence', example: 'DH544G'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:supplier-product', 'write:supplier-product'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Produit', readableLink: false, example: '/api/product/1'),
        ORM\ManyToOne(targetEntity: TechnicalSheet::class, inversedBy: 'supplierProducts'),
        Serializer\Groups(['read:supplier-product', 'write:supplier-product'])
    ]
    private ?TechnicalSheet $product = null;

    #[
        ApiProperty(description: 'Poids cuivre', openapiContext: ['$ref' => '#/components/schemas/Measure-linear-density']),
        ORM\Embedded,
        Serializer\Groups(['read:supplier-product', 'write:supplier-product'])
    ]
    private Measure $copperWeight;

    #[
        ApiProperty(description: 'Temps de livraison', openapiContext: ['$ref' => '#/components/schemas/Measure-duration']),
        ORM\Embedded,
        Serializer\Groups(['read:supplier-product', 'write:supplier-product'])
    ]
    private Measure $deliveryTime;

    #[
        ApiProperty(description: 'Incoterms', readableLink: false, example: '/api/incoterms/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:supplier-product', 'write:supplier-product'])
    ]
    private ?Incoterms $incoterms = null;

    #[
        ApiProperty(description: 'Indice', example: '0'),
        ORM\Column(name: '`index`', options: ['default' => '0']),
        Serializer\Groups(['read:supplier-product', 'write:supplier-product'])
    ]
    private string $index = '0';

    #[
        ApiProperty(description: 'MOQ (Minimal Order Quantity)', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:supplier-product', 'write:supplier-product'])
    ]
    private Measure $moq;

    #[
        ApiProperty(description: 'Conditionnement', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:supplier-product', 'write:supplier-product'])
    ]
    private Measure $packaging;

    #[
        ApiProperty(description: 'Type de packaging', example: 'Palette'),
        ORM\Column(length: 30, nullable: true),
        Serializer\Groups(['read:supplier-product', 'write:supplier-product'])
    ]
    private ?string $packagingKind = null;

    /** @var DoctrineCollection<int, SupplierProductPrice> */
    #[
        ORM\OneToMany(mappedBy: 'product', targetEntity: SupplierProductPrice::class, fetch: 'EAGER')
    ]
    private DoctrineCollection $prices;

    #[
        ApiProperty(description: 'Proportion', example: '99'),
        ORM\Column(options: ['default' => 100, 'unsigned' => true]),
        Serializer\Groups(['read:supplier-product', 'write:supplier-product'])
    ]
    private float $proportion = 100;

    #[
        ApiProperty(description: 'Fournisseur', readableLink: false, example: '/api/suppliers/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:supplier-product', 'write:supplier-product'])
    ]
    private ?Supplier $supplier = null;
   

    public function __construct() {
        $this->copperWeight = new Measure();
        $this->deliveryTime = new Measure();
        $this->moq = new Measure();
        $this->packaging = new Measure();
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

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getProduct(): ?TechnicalSheet {
        return $this->product;
    }

    final public function getCopperWeight(): Measure {
        return $this->copperWeight;
    }

    final public function getDeliveryTime(): Measure {
        return $this->deliveryTime;
    }

    final public function getIncoterms(): ?Incoterms {
        return $this->incoterms;
    }

    final public function getIndex(): string {
        return $this->index;
    }

    final public function getMoq(): Measure {
        return $this->moq;
    }

    final public function getPackaging(): Measure {
        return $this->packaging;
    }

    final public function getPackagingKind(): ?string {
        return $this->packagingKind;
    }

    public function getPrices()
    {
        return $this->prices;
    }

    final public function getProportion(): float {
        return $this->proportion;
    }

    final public function getSupplier(): ?Supplier {
        return $this->supplier;
    }

    final public function getUnit(): ?Unit {
        return $this->product?->getUnit();
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setProduct(?TechnicalSheet $product): self {
        $this->product = $product;
        return $this;
    }

    final public function setCopperWeight(Measure $copperWeight): self {
        $this->copperWeight = $copperWeight;
        return $this;
    }

    final public function setDeliveryTime(Measure $deliveryTime): self {
        $this->deliveryTime = $deliveryTime;
        return $this;
    }

    final public function setIncoterms(?Incoterms $incoterms): self {
        $this->incoterms = $incoterms;
        return $this;
    }

    final public function setIndex(string $index): self {
        $this->index = $index;
        return $this;
    }

    final public function setMoq(Measure $moq): self {
        $this->moq = $moq;
        return $this;
    }

    final public function setPackaging(Measure $packaging): self {
        $this->packaging = $packaging;
        return $this;
    }

    final public function setPackagingKind(?string $packagingKind): self {
        $this->packagingKind = $packagingKind;
        return $this;
    }

    final public function setProportion(float $proportion): self {
        $this->proportion = $proportion;
        return $this;
    }

    final public function setSupplier(?Supplier $supplier): self {
        $this->supplier = $supplier;
        return $this;
    }

}