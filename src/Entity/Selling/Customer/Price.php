<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Traits\NotesTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

 #[
    ApiResource(
        description: 'Prix',
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
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un prix',
                    'summary' => 'Modifie un prix',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        shortName: 'CustomerPrice',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:price', 'write:notes', 'write:price', 'write:product', 'write:measure', 'write:unit'],
            'openapi_definition_name' => 'CustomerPrice-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:notes', 'read:price', 'read:product', 'read:measure', 'read:unit'],
            'openapi_definition_name' => 'CustomerPrice-read'
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'product_customer_price')
]
class Price extends Entity {
    use NotesTrait;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Lorem ipsum dolores it'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:notes', 'write:notes'])
    ]
    protected ?string $notes = null;

    #[
        ApiProperty(description: 'Prix', example: 52),
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 10, 'unsigned' => true], type: 'float'),
        Serializer\Groups(['read:price', 'write:price'])
    ]
    private float $price = 0;

    #[
        ApiProperty(description: 'Produit', required: true, readableLink: false, example: '/api/products/4'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Product::class),
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private ?Product $product;

    #[
        ApiProperty(description: 'Quantité', example: '3'),
        ORM\Embedded(Measure::class),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private Measure $quantity;

    public function __construct() {
        $this->quantity = new Measure();
    }

    final public function getPrice(): float {
        return $this->price;
    }

    final public function getProduct(): ?Product {
        return $this->product;
    }

    final public function getQuantity(): Measure {
        return $this->quantity;
    }

    final public function setPrice(float $price): self {
        $this->price = $price;
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
