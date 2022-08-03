<?php

namespace App\Entity\Logistics\Stock;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Project\Product\Product;
use App\Filter\RelationFilter;
use App\Repository\Logistics\Stock\ProductStockRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template-extends Stock<Product>
 */
#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['item', 'warehouse']),
    ApiResource(
        description: 'Stock des produits',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les stocks de produits',
                    'summary' => 'Récupère les stocks de produits',
                    'tags' => ['Stock']
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un stock de produits',
                    'summary' => 'Créer un stock de produits',
                    'tags' => ['Stock']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')'
            ]
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        shortName: 'ProductStock',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:stock'],
            'openapi_definition_name' => 'ProductStock-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:stock'],
            'openapi_definition_name' => 'ProductStock-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity(repositoryClass: ProductStockRepository::class)
]
class ProductStock extends Stock {
    #[
        ApiProperty(description: 'Produit', readableLink: false, example: '/api/products/1'),
        ORM\JoinColumn(name: 'product_id'),
        ORM\ManyToOne(targetEntity: Product::class),
        Serializer\Groups(['read:stock', 'write:stock'])
    ]
    protected $item;
}
