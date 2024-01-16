<?php

namespace App\Entity\Purchase\Component\Equivalent;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Purchase\Component\Component;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use App\Filter\RelationFilter;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['componentEquivalent', 'component']),
    ApiResource(
        description: 'Item de Groupe de composant équivalent',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les items de groupe de composant équivalent',
                    'summary' => 'Récupère les items de groupe de composant équivalent',
                ],
                'normalization_context' => [
                    'groups' => ['read:component-equivalent-item:collection'],
                    'openapi_definition_name' => 'ComponentEquivalentItem-collection',
                    'skip_null_values' => false
                ],
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un item de groupe de composant équivalent',
                    'summary' => 'Créer un item de groupe de composant équivalent',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')',
                'denormalization_context' => [
                    'groups' => ['create:component-equivalent-item'],
                    'openapi_definition_name' => 'ComponentEquivalentItem-create'
                ],
                'validation_groups' => ['ComponentEquivalentItem-create']
            ]
        ],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un item de groupe de composant équivalent',
                    'summary' => 'Récupère un item de groupe de composant équivalent',
                ],
            ],
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un item de groupe de composant équivalent',
                    'summary' => 'Supprime un item de groupe de composant équivalent',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')',
        ],
        denormalizationContext: [
            'groups' => ['create:component-equivalent-item', 'write:component-equivalent-item'],
            'openapi_definition_name' => 'ComponentEquivalentItem-write'
        ],
        normalizationContext: [
            'groups' => ['read:component-equivalent-item', 'read:component-equivalent-item:collection'],
            'openapi_definition_name' => 'ComponentEquivalentItem-read',
            'skip_null_values' => false
        ],
        paginationClientEnabled: true
    ),
    ORM\Entity()
]
class ComponentEquivalentItem extends Entity
{
    #[
        ApiProperty(description: 'Groupe d\'équivalence', required: true, example: '/api/component-equivalents/1'),
        ORM\ManyToOne(targetEntity: ComponentEquivalent::class, inversedBy: 'items'),
        Serializer\Groups(['read:component-equivalent-item', 'read:component-equivalent-item:collection', 'write:component-equivalent-item'])
        ]
    private ComponentEquivalent $componentEquivalent;

    #[
        ApiProperty(description: 'Composant', required: true, example: '/api/components/1'),
        ORM\ManyToOne(targetEntity: Component::class),
        Serializer\Groups(['read:component-equivalent-item', 'read:component-equivalent-item:collection', 'write:component-equivalent-item'])
    ]
    private Component $component;

    public function getComponentEquivalent(): ComponentEquivalent
    {
        return $this->componentEquivalent;
    }

    public function setComponentEquivalent(ComponentEquivalent $componentEquivalent): ComponentEquivalentItem
    {
        $this->componentEquivalent = $componentEquivalent;
        return $this;
    }

    public function getComponent(): Component
    {
        return $this->component;
    }

    public function setComponent(Component $component): ComponentEquivalentItem
    {
        $this->component = $component;
        return $this;
    }
}