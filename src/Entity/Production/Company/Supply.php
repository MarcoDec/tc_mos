<?php

namespace App\Entity\Production\Company;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Logistics\Incoterms;
use App\Entity\Management\Society\Company;
use App\Entity\Project\Product\Product;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['company', 'product']),
    ApiResource(
        description: 'Fourniture',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les fournitures',
                    'summary' => 'Récupère les fournitures',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une fourniture',
                    'summary' => 'Créer une fourniture',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une fourniture',
                    'summary' => 'Supprime une fourniture',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une fourniture',
                    'summary' => 'Modifie une fourniture',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:supply'],
            'openapi_definition_name' => 'Supply-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:supply'],
            'openapi_definition_name' => 'Supply-read',
            'skip_null_values' => false
        ],
        paginationEnabled: false
    ),
    ORM\Entity
]
class Supply extends Entity {
    #[
        ApiProperty(description: 'Compagnie', example: '/api/companies/1'),
        Assert\NotBlank,
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['read:supply', 'write:supply'])
    ]
    private ?Company $company;

    #[
        ApiProperty(description: 'Incoterms', readableLink: false, example: '/api/incoterms/2'),
        Assert\NotBlank,
        ORM\ManyToOne,
        Serializer\Groups(['read:supply', 'write:supply'])
    ]
    private ?Incoterms $incoterms = null;

    #[
        ApiProperty(description: 'Produit', readableLink: false, example: '/api/products/4'),
        Assert\NotBlank,
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['read:supply', 'write:supply'])
    ]
    private ?Product $product = null;

    #[
        ApiProperty(description: 'Proportion', example: '99'),
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 100, 'unsigned' => true]),
        Serializer\Groups(['read:supply', 'write:supply'])
    ]
    private float $proportion = 100;

    #[
        ApiProperty(description: 'Référence', example: 'FIZ56'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:supply', 'write:supply'])
    ]
    private ?string $ref = null;

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getIncoterms(): ?Incoterms {
        return $this->incoterms;
    }

    final public function getProduct(): ?Product {
        return $this->product;
    }

    final public function getProportion(): float {
        return $this->proportion;
    }

    final public function getRef(): ?string {
        return $this->ref;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setIncoterms(?Incoterms $incoterms): self {
        $this->incoterms = $incoterms;
        return $this;
    }

    final public function setProduct(?Product $product): self {
        $this->product = $product;
        return $this;
    }

    final public function setProportion(float $proportion): self {
        $this->proportion = $proportion;
        return $this;
    }

    final public function setRef(?string $ref): self {
        $this->ref = $ref;
        return $this;
    }
}
