<?php

namespace App\Entity\Purchase\Supplier\Price;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Entity\Purchase\Supplier\Product;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Filter\RelationFilter;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['product']),
    ApiResource(
        description: 'Prix',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les prix produit fournisseur',
                    'summary' => 'Récupère les prix produit fournisseur',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un prix produit fournisseur',
                    'summary' => 'Créer un prix produit fournisseur',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un prix produit fournisseur',
                    'summary' => 'Supprime un prix produit fournisseur',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un prix produit fournisseur',
                    'summary' => 'Modifie un prix produit fournisseur',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        shortName: 'SupplierProductPrice',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:price'],
            'openapi_definition_name' => 'SupplierProductPrice-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:price'],
            'openapi_definition_name' => 'SupplierProductPrice-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'supplier_product_price')
]
class ProductPrice extends Entity implements MeasuredInterface {
    #[
        ApiProperty(description: 'Référence', example: 'DJZ54'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:price', 'write:price'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Produit fournisseur', readableLink: false, example: '/api/supplier-products/1'),
        ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'prices'),
        Serializer\Groups(['read:price', 'write:price'])
    ]
    private ?Product $product = null;

    #[
        ApiProperty(description: 'Prix', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:price', 'write:price'])
    ]
    private Measure $price;

    #[
        ApiProperty(description: 'Quantité', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        AppAssert\Measure,
        ORM\Embedded,
        Serializer\Groups(['read:price', 'write:price'])
    ]
    private Measure $quantity;

    public function __construct() {
        $this->price = new Measure();
        $this->quantity = new Measure();
    }

    final public function getProduct(): ?Product {
        return $this->product;
    }

    final public function getMeasures(): array {
        return [$this->price, $this->quantity];
    }
    final public function getUnitMeasures(): array
    {
        return [$this->quantity];
    }
    final public function getCurrencyMeasures(): array
    {
        return [$this->price];
    }

    final public function getPrice(): Measure {
        return $this->price;
    }

    final public function getQuantity(): Measure {
        return $this->quantity;
    }

    final public function getRef(): ?string {
        return $this->ref;
    }

    final public function getUnit(): ?Unit {
        return $this->product?->getUnit();
    }

    final public function setProduct(?Product $product): self {
        $this->product = $product;
        return $this;
    }

    final public function setPrice(Measure $price): self {
        $this->price = $price;
        return $this;
    }

    final public function setQuantity(Measure $quantity): self {
        $this->quantity = $quantity;
        return $this;
    }

    final public function setRef(?string $ref): self {
        $this->ref = $ref;
        return $this;
    }
}
