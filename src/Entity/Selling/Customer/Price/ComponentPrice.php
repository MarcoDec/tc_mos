<?php

namespace App\Entity\Selling\Customer\Price;

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
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[
    ApiFilter(SearchFilter::class, properties: ['component' => 'exact']),
    ApiResource(
        description: 'Grille tarifaire composant',
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
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un prix',
                    'summary' => 'Modifie un prix',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        shortName: 'CustomerComponentPrice',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:price'],
            'openapi_definition_name' => 'CustomerComponentPrice-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:price'],
            'openapi_definition_name' => 'CustomerComponentPrice-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'customer_component_price')
]
class ComponentPrice extends Entity implements MeasuredInterface {
    //region properties
    #[
        ApiProperty(description: 'Prix', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:price', 'write:price'])
    ]
    private Measure $price;

    #[
        ApiProperty(description: 'Composant', readableLink: false, example: '/api/customer-components/1'),
        ORM\ManyToOne(targetEntity: Component::class, inversedBy: 'componentPrices'),
        Serializer\Groups(['read:price', 'write:price'])
    ]
    private ?Component $component = null;

    #[
        ApiProperty(description: 'Quantité', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        AppAssert\Measure,
        ORM\Embedded,
        Serializer\Groups(['read:price', 'write:price'])
    ]
    private Measure $quantity;
    //endregion
    public function __construct() {
        $this->price = new Measure();
        $this->quantity = new Measure();
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

    final public function getPrice(): Measure {
        return $this->price;
    }

    final public function getComponent(): ?Component {
        return $this->component;
    }

    final public function getQuantity(): Measure {
        return $this->quantity;
    }

    final public function getUnit(): ?Unit {
        return $this->component?->getUnit();
    }

    final public function setPrice(Measure $price): self {
        $this->price = $price;
        return $this;
    }

    final public function setComponent(?Component $component): self {
        $this->component = $component;
        return $this;
    }

    final public function setQuantity(Measure $quantity): self {
        $this->quantity = $quantity;
        return $this;
    }
    //endregion
}
