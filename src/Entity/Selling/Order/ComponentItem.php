<?php

namespace App\Entity\Selling\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Purchase\Component\Component;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template-extends Item<Component>
 */
#[
    ApiResource(
        description: 'Ligne de commande',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une ligne',
                    'summary' => 'Créer une ligne',
                    'tags' => ['CustomerOrderItem']
                ]
            ]
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        shortName: 'CustomerOrderItemComponent',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:item', 'write:measure'],
            'openapi_definition_name' => 'CustomerOrderItemComponent-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:measure'],
            'openapi_definition_name' => 'CustomerOrderItemComponent-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class ComponentItem extends Item {
    #[
        ApiProperty(description: 'Composant', example: '/api/components/1'),
        ORM\JoinColumn(name: 'component_id'),
        ORM\ManyToOne(targetEntity: Component::class),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected $item;
}
