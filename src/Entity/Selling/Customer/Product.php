<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Project\Product\Product as TechnicalSheet;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'customer' => 'name',
        'product' => 'name'
    ]),
    ApiResource(
        description: 'Produit',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les produits',
                    'summary' => 'Récupère les produits',
                ],
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un produit',
                    'summary' => 'Créer un produit',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')',
                // 'denormalization_context' => [
                //     'groups' => ['write:name', 'write:event_date', 'write:customer-event']
                // ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un produit',
                    'summary' => 'Supprime un produit',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un produit',
                    'summary' => 'Récupère un produit',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un produit',
                    'summary' => 'Modifie un produit',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ],
        ],
        shortName: 'CustomerProduct',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:customer', 'write:product'],
            'openapi_definition_name' => 'CustomerProduct-write'
        ],
        normalizationContext: [
            'groups' => ['read:customer', 'read:product', 'read:id'],
            'openapi_definition_name' => 'CustomerProduct-read'
        ],
    ),
    ORM\Entity,
    ORM\Table(name: 'product_customer')
]
class Product extends Entity {
    #[
        ApiProperty(description: 'Client', required: false, readableLink: false, example: '/api/customers/8'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Customer::class),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private ?Customer $customer;

    #[
        ApiProperty(description: 'Client', required: false, readableLink: false, example: '/api/products/45'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: TechnicalSheet::class),
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private ?TechnicalSheet $product;

    final public function getCustomer(): ?Customer {
        return $this->customer;
    }

    final public function getProduct(): ?TechnicalSheet {
        return $this->product;
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
