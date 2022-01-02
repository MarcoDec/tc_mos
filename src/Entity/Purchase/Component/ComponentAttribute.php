<?php

namespace App\Entity\Purchase\Component;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'component' => ['name', 'required' => true],
        'attribute' => 'name'
    ]),
    ApiFilter(filterClass: SearchFilter::class, properties: ['value' => 'partial']),
    ApiResource(
        description: 'Caractéristiques du composant',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les caractéristiques du composant',
                    'summary' => 'Récupère les caractéristiques du composant',
                ],
            ],
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une caractéristique du composant',
                    'summary' => 'Supprime une caractéristique du composant',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une caractéristique du composant',
                    'summary' => 'Modifie une caractéristique du composant',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:attribute', 'write:component', 'write:component-attribute'],
            'openapi_definition_name' => 'ComponentAttribute-write'
        ],
        normalizationContext: [
            'groups' => ['read:attribute', 'read:id', 'read:component', 'read:component-attribute'],
            'openapi_definition_name' => 'ComponentAttribute-read'
        ],
    ),
    ORM\Entity
]
class ComponentAttribute extends Entity {
    #[
        ApiProperty(description: 'Attribut', readableLink: false, example: '/api/attributes/1'),
        ORM\ManyToOne(targetEntity: Attribute::class),
        Serializer\Groups(['read:attribute', 'write:attribute'])
    ]
    private ?Attribute $attribute;

    #[
        ApiProperty(description: 'Composant', readableLink: false, example: '/api/components/5'),
        ORM\ManyToOne(targetEntity: Component::class),
        Serializer\Groups(['read:component', 'write:component'])
    ]
    private ?Component $component;

    #[
        ApiProperty(description: 'Valeur', example: '200'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:component-attribute', 'write:component-attribute'])
    ]
    private ?string $value;

    final public function getAttribute(): ?Attribute {
        return $this->attribute;
    }

    final public function getComponent(): ?Component {
        return $this->component;
    }

    final public function getValue(): ?string {
        return $this->value;
    }

    final public function setAttribute(?Attribute $attribute): self {
        $this->attribute = $attribute;

        return $this;
    }

    final public function setComponent(?Component $component): self {
        $this->component = $component;

        return $this;
    }

    final public function setValue(?string $value): self {
        $this->value = $value;

        return $this;
    }
}
