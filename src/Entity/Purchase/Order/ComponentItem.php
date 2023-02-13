<?php

namespace App\Entity\Purchase\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Purchase\Component\Component;
use App\Repository\Purchase\Order\ComponentItemRepository;
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
                    'tags' => ['PurchaseOrderItem']
                ]
            ]
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        shortName: 'PurchaseOrderItemComponent',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:item', 'write:measure'],
            'openapi_definition_name' => 'PurchaseOrderItemComponent-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:measure'],
            'openapi_definition_name' => 'PurchaseOrderItemComponent-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity(repositoryClass: ComponentItemRepository::class)
]
class ComponentItem extends Item {
    #[
        ApiProperty(description: 'Composant', example: '/api/components/1'),
        ORM\JoinColumn(name: 'component_id'),
        ORM\ManyToOne(targetEntity: Component::class, fetch: "EAGER"),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected $item;
}
