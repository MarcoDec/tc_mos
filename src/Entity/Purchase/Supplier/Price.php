<?php

namespace App\Entity\Purchase\Supplier;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use App\Entity\Purchase\Supplier\Component as SupplierComponent;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Filter\RelationFilter;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['component']),
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
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un prix',
                    'summary' => 'Modifie un prix',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        shortName: 'SupplierComponentPrice',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:price'],
            'openapi_definition_name' => 'SupplierComponentPrice-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:price'],
            'openapi_definition_name' => 'SupplierComponentPrice-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'supplier_component_price')
]
class Price extends Entity implements MeasuredInterface {
    #[
        ApiProperty(description: 'Référence', example: 'DJZ54'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:price', 'write:price'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Composant', readableLink: false, example: '/api/supplier-components/1'),
        ORM\ManyToOne(targetEntity: SupplierComponent::class, inversedBy: 'prices'),
        Serializer\Groups(['read:price', 'write:price'])
    ]
    private ?Component $component = null;

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

    final public function getComponent(): ?Component {
        return $this->component;
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
        return $this->component?->getUnit();
    }

    final public function setComponent(?Component $component): self {
        $this->component = $component;
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
