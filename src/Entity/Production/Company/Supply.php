<?php

namespace App\Entity\Production\Company;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Logistics\Incoterms;
use App\Entity\Management\Society\Company;
use App\Entity\Project\Product\Product;
use App\Entity\Traits\CompanyTrait;
use App\Entity\Traits\RefTrait;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'ref' => 'partial',
    ]),
    ApiFilter(filterClass: NumericFilter::class, properties: ['proportion']),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'product' => ['name', 'required' => true],
        'company' => 'name',
        'incoterms' => 'name'
    ]),
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
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une fourniture',
                    'summary' => 'Modifie une fourniture',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ]
        ],
        shortName: 'CompanySupply',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:incoterms', 'write:company', 'write:company-supply', 'write:ref', 'write:product'],
            'openapi_definition_name' => 'CompanySupply-write'
        ],
        normalizationContext: [
            'groups' => ['read:incoterms', 'read:id', 'read:company', 'read:company-supply', 'read:ref', 'read:product'],
            'openapi_definition_name' => 'CompanySupply-read'
        ],
    ),
    ORM\Entity
]
class Supply extends Entity {
    use CompanyTrait;
    use RefTrait;

    #[
        ApiProperty(description: 'Company', required: false, example: '/api/companies/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Company::class),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    protected ?Company $company;

    #[
        ApiProperty(description: 'Référence', required: false, example: 'FIZ56'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:ref', 'write:ref'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Incoterms', required: true, readableLink: false, example: '/api/incoterms/2'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Incoterms::class),
        Serializer\Groups(['read:incoterms', 'write:incoterms'])
    ]
    private ?Incoterms $incoterms;

    #[
        ApiProperty(description: 'Produit', required: true, readableLink: false, example: '/api/products/4'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Product::class),
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private ?Product $product;

    #[
        ApiProperty(description: 'Proportion', required: true, example: '99'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 100, 'unsigned' => true], type: 'float'),
        Serializer\Groups(['read:company-supply', 'write:company-supply'])
    ]
    private float $proportion = 100;

    public function getIncoterms(): ?Incoterms {
        return $this->incoterms;
    }

    public function getProduct(): ?Product {
        return $this->product;
    }

    public function getProportion(): ?float {
        return $this->proportion;
    }

    public function setIncoterms(?Incoterms $incoterms): self {
        $this->incoterms = $incoterms;

        return $this;
    }

    public function setProduct(?Product $product): self {
        $this->product = $product;

        return $this;
    }

    public function setProportion(float $proportion): self {
        $this->proportion = $proportion;

        return $this;
    }
}
