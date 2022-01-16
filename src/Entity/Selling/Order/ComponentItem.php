<?php

namespace App\Entity\Selling\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Purchase\Component\Component;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Item du composant',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un item de composant',
                    'summary' => 'Créer un item de composant',
                ]
            ],
        ],
        itemOperations: [
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
        ],
        shortName: 'SellingOrderComponent',
        denormalizationContext: [
            'groups' => ['write:item', 'write:order', 'write:current_place', 'write:notes', 'write:ref', 'write:name', 'write:component'],
            'openapi_definition_name' => 'SellingOrderItem-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:item', 'read:order', 'read:current_place', 'read:notes', 'read:ref', 'read:name', 'read:component'],
            'openapi_definition_name' => 'SellingOrderItem-read'
        ],
    ),
    ORM\Entity
]
class ComponentItem extends Item {
    #[
        ApiProperty(description: 'Composant', required: false, example: '/api/components/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Component::class),
        Serializer\Groups(['read:component', 'write:component'])
    ]
    protected $item;
}
