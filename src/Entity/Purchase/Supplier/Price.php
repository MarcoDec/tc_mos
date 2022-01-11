<?php

namespace App\Entity\Purchase\Supplier;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Traits\RefTrait;
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
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un prix',
                    'summary' => 'Supprime un prix',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un prix',
                    'summary' => 'Modifie un prix',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        shortName: 'SupplierPrice',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:price', 'write:ref', 'write:price', 'write:component', 'write:measure', 'write:unit'],
            'openapi_definition_name' => 'SupplierPrice-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:ref', 'read:price', 'read:component', 'read:measure', 'read:unit'],
            'openapi_definition_name' => 'SupplierPrice-read'
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'component_supplier_price')
]
class Price extends Entity {
    use RefTrait;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Coût du produit'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:price', 'write:price'])
    ]
    protected ?string $comments = null;

    #[
        ApiProperty(description: 'Référence', example: 'DJZ54'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:ref', 'write:ref'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Composant', required: false, readableLink: false, example: '/api/components/2'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Component::class),
        Serializer\Groups(['read:component', 'write:component'])
    ]
    private ?Component $component;

    #[
        ApiProperty(description: 'Prix', example: 52),
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 10, 'unsigned' => true], type: 'float'),
        Serializer\Groups(['read:price', 'write:price'])
    ]
    private float $price = 0;

    #[
        ApiProperty(description: 'Quantité', example: '3'),
        ORM\Embedded(Measure::class),
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private Measure $quantity;

    public function __construct() {
        $this->quantity = new Measure();
    }

    final public function getComments(): ?string {
        return $this->comments;
    }

    final public function getComponent(): ?Component {
        return $this->component;
    }

    final public function getPrice(): float {
        return $this->price;
    }

    final public function getQuantity(): Measure {
        return $this->quantity;
    }

    final public function setComments(?string $comments): self {
        $this->comments = $comments;
        return $this;
    }

    final public function setComponent(?Component $component): self {
        $this->component = $component;
        return $this;
    }

    final public function setPrice(float $price): self {
        $this->price = $price;
        return $this;
    }

    final public function setQuantity(measure $quantity): self {
        $this->quantity = $quantity;
        return $this;
    }
}
