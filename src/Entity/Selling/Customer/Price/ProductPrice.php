<?php

namespace App\Entity\Selling\Customer\Price;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Entity\Traits\Price\ItemPriceTrait;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[
    ApiFilter(SearchFilter::class, properties: ['product' => 'exact']),
    ApiResource(
        description: 'Grille tarifaire produit',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les prix',
                    'summary' => 'Récupère les prix',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un prix',
                    'summary' => 'Créer un prix',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un prix',
                    'summary' => 'Supprime un prix',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_ADMIN.'\')'
            ],
            'get',
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un prix',
                    'summary' => 'Modifie un prix',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        shortName: 'CustomerProductPrice',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:price'],
            'openapi_definition_name' => 'CustomerProductPrice-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:price'],
            'openapi_definition_name' => 'CustomerProductPrice-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'customer_product_price')
]
class ProductPrice extends Entity implements MeasuredInterface {
    use ItemPriceTrait;
    //region properties
    #[
        ApiProperty(description: 'Produit', readableLink: false, example: '/api/customer-products/1'),
        ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'prices'),
        Serializer\Groups(['read:price', 'write:price'])
    ]
    private ?Product $product = null;

    //endregion
    public function __construct() {
        $this->initialize();
    }
    //region getters & setters
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
    final public function getProduct(): ?Product {
        return $this->product;
    }

    final public function getUnit(): ?Unit {
        return $this->product?->getUnit();
    }

    final public function setProduct(?Product $product): self {
        $this->product = $product;
        return $this;
    }

    //endregion
}
