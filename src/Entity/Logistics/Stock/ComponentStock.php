<?php

namespace App\Entity\Logistics\Stock;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Doctrine\DBAL\Types\ItemType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Purchase\Component\Component;
use App\Filter\RelationFilter;
use App\Repository\Logistics\Stock\ComponentStockRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template-extends Stock<Component>
 */
#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['item', 'warehouse']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['batchNumber' => 'partial']),
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
            'receipt' => [
                'controller' => PlaceholderAction::class,
                'denormalization_context' => [
                    'groups' => ['receipt:stock', 'write:measure'],
                    'openapi_definition_name' => 'ComponentStock-receipt'
                ],
                'method' => 'POST',
                'openapi_context' => [
                    'description' => 'Réceptionne une commande',
                    'summary' => 'Réceptionne une commande',
                    'tags' => ['Stock']
                ],
                'path' => '/component-stocks/receipt',
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')'
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
            'groups' => ['read:measure', 'read:stock'],
            'openapi_definition_name' => 'ComponentStock-read',
            'skip_null_values' => false
        ],
        paginationClientEnabled: true
    ),
    ORM\Entity(repositoryClass: ComponentStockRepository::class)
]
class ComponentStock extends Stock {
    #[
        ApiProperty(description: 'Composant', example: '/api/components/1'),
        ORM\JoinColumn(name: 'component_id'),
        ORM\ManyToOne(targetEntity: Component::class),
        Serializer\Groups(['read:stock', 'read:stock:grouped', 'write:stock'])
    ]
    protected $item;

    final protected function getType(): string {
        return ItemType::TYPE_COMPONENT;
    }
}
