<?php

namespace App\Entity\Selling\Order;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Purchase\Component\Component;
use App\Filter\RelationFilter;
use App\Repository\Selling\Order\ComponentItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @template-extends Item<Component>
 */
#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['item',  'sellingOrder.customer.id']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['sellingOrder.customer.id' => 'partial', 'ref' => 'partial', 'requestedQuantity.value' => 'partial', 'requestedQuantity.code' => 'partial', 'confirmedQuantity.code' => 'partial', 'confirmedQuantity.value' => 'partial', 'confirmedDate' => 'partial', 'requestedDate' => 'partial',
    'sellingOrder.ref' => 'partial', 'embState.state' =>'partial', 'sellingOrder.kind' => 'partial', 'item.id'=> 'partial'
    ]),
    ApiResource(
        description: 'Ligne de commande',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une ligne',
                    'summary' => 'Créer une ligne',
                    'tags' => ['SellingOrderItem']
                ]
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les lignes',
                    'summary' => 'Récupère les lignes',
                ],
                'path' => '/selling-order-components',
            ]
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        shortName: 'SellingOrderItemComponent',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:item', 'write:measure'],
            'openapi_definition_name' => 'SellingOrderItemComponent-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:measure', 'read:state'],
            'openapi_definition_name' => 'SellingOrderItemComponent-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity(repositoryClass: ComponentItemRepository::class)
]
class ComponentItem extends Item {
    #[
        ApiProperty(description: 'Composant', readableLink: true, fetchEager: true),
        ORM\JoinColumn(name: 'component_id'),
        ORM\ManyToOne(targetEntity: Component::class),
        Serializer\Groups(['read:item', 'write:item']),
        Serializer\MaxDepth(1)
    ]
    protected $item;
    public function __construct()
    {
        parent::__construct();
        $this->item = new Component();
    }
}
