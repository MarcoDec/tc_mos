<?php

namespace App\Entity\Project\Product;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: [
        'mandated'
    ]),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'product' => ['name', 'required' => true],
        'quantity' => 'value'
    ]),
    ApiResource(
        description: 'Produit',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les nomenclatures',
                    'summary' => 'Récupère les nomenclatures',
                ],
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une nomenclature',
                    'summary' => 'Créer une nomenclature',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une nomenclature',
                    'summary' => 'Supprime une nomenclature',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une nomenclature',
                    'summary' => 'Récupère une nomenclature',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une nomenclature',
                    'summary' => 'Modifie une nomenclature',
                ],
            ],

        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PROJECT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:product', 'write:nomenclature', 'write:measure'],
            'openapi_definition_name' => 'Nomenclature-write'
        ],
        normalizationContext: [
            'groups' => ['read:product', 'read:id', 'read:nomenclature', 'read:measure'],
            'openapi_definition_name' => 'Nomenclature-read'
        ],
    ),
    ORM\Entity
]
class Nomenclature extends Entity {
    #[
        ApiProperty(description: 'Obligatoire', required: false, example: true),
        ORM\Column(options: ['default' => true], type: 'boolean'),
        Serializer\Groups(['read:nomenclature', 'write:nomenclature'])
    ]
    private bool $mandated = true;

    #[
        ApiProperty(description: 'Produit', readableLink: false, example: '/api/products/58'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Product::class),
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private ?Product $product;

    #[
        ApiProperty(description: 'Quantité', example: '54'),
        ORM\Embedded(Measure::class),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private Measure $quantity;

    public function __construct() {
        $this->quantity = new Measure();
    }

    final public function getMandated(): ?bool {
        return $this->mandated;
    }

    final public function getProduct(): ?Product {
        return $this->product;
    }

    final public function getQuantity(): Measure {
        return $this->quantity;
    }

    final public function setMandated(bool $mandated): self {
        $this->mandated = $mandated;

        return $this;
    }

    final public function setProduct(?Product $product): self {
        $this->product = $product;

        return $this;
    }

    final public function setQuantity(Measure $quantity): self {
        $this->quantity = $quantity;

        return $this;
    }
}
