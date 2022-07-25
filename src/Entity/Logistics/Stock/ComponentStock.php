<?php

namespace App\Entity\Logistics\Stock;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Purchase\Component\Component;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template-extends Stock<Component>
 */
#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['item', 'warehouse']),
    ApiResource(
        description: 'Stock des composants',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les stocks de composants',
                    'summary' => 'Récupère les stocks de composants',
                    'tags' => ['Stock']
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un stock de composants',
                    'summary' => 'Créer un stock de composants',
                    'tags' => ['Stock']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')'
            ]
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        shortName: 'ComponentStock',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:stock'],
            'openapi_definition_name' => 'ComponentStock-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:stock'],
            'openapi_definition_name' => 'ComponentStock-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class ComponentStock extends Stock {
    #[
        ApiProperty(description: 'Composant', readableLink: false, example: '/api/components/1'),
        ORM\JoinColumn(name: 'component_id'),
        ORM\ManyToOne(targetEntity: Component::class),
        Serializer\Groups(['read:stock', 'write:stock'])
    ]
    protected $item;
}
