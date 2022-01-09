<?php

namespace App\Entity\Logistics\Stock;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Project\Product\Product;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Stock des produits',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un stock de produits',
                    'summary' => 'Créer un stock de produits',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')'
            ]
        ],
        itemOperations: [
        ],
        shortName: 'ProductStock',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:stock', 'write:warehouse', 'write:measure', 'write:name', 'write:unit', 'write:company'],
            'openapi_definition_name' => 'ProductStock-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:stock', 'read:warehouse', 'read:measure', 'read:name', 'read:unit', 'read:company'],
            'openapi_definition_name' => 'ProductStock-read'
        ],
    ),
    ORM\Entity
]
class ProductStock extends Stock {
    #[
        ApiProperty(description: 'Produit', required: false, readableLink: false, example: '/api/products/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Product::class),
        Serializer\Groups(['read:stock', 'write:stock'])
    ]
    private ?Product $item;

    final public function getItem(): ?Product {
        return $this->item;
    }

    final public function getItemType(): string {
        return 'Product';
    }

    final public function setItem(?Product $item): self {
        $this->item = $item;

        return $this;
    }
}
