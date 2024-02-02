<?php

namespace App\Entity\Purchase\Order;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Doctrine\DBAL\Types\ItemType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Purchase\Component\Component;
use App\Filter\RelationFilter;
use App\Repository\Purchase\Order\ComponentItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;



/**
 * @template-extends Item<Component>  ApiFilter(filterClass: SearchFilter::class, properties: ['confirmedDate' => 'partial', 'requestedDate' => 'partial', 'confirmedQuantity' => 'partial', 'requestedQuantity' => 'partial']),
 */
#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['confirmedDate', 'confirmedQuantity.value', 'item.code', 'item.manufacturerCode','requestedQuantity.value', 'requestedDate', 'targetCompany']),
    ApiResource(
        description: 'Ligne de commande',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les fournisseurs de composants',
                    'summary' => 'Récupère les fournisseurs de composants',
                    'tags' => ['PurchaseOrderItem']
                ]
            ],
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
    ORM\DiscriminatorColumn(name: 'type', type: 'item'),
    ORM\Entity(repositoryClass: ComponentItemRepository::class)
]
class ComponentItem extends Item {

    #[
        ApiProperty(description: 'Composant', example: '/api/components/1'),
        ORM\JoinColumn(name: 'component_id'),
        ORM\ManyToOne(targetEntity: Component::class),
        Serializer\Groups(['read:item', 'write:item'])
    ]
    protected $item;

    final protected function getType(): string {
        return ItemType::TYPE_COMPONENT;
    }

}
