<?php

namespace App\Entity\Quality\Reception\Reference\Selling;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Project\Product\Product;
use App\Entity\Quality\Reception\Reference\Reference;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template-extends Reference<Product>
 */
#[
    ApiResource(
        description: 'Définition d\'un contrôle réception',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un contrôle réception',
                    'summary' => 'Créer un contrôle réception',
                    'tags' => ['Reference']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_WRITER.'\')'
            ]
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_QUALITY_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:reference'],
            'openapi_definition_name' => 'ComponentReference-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:reference'],
            'openapi_definition_name' => 'ComponentReference-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class ProductReference extends Reference {
    #[
        ApiProperty(description: 'Produits', readableLink: false, example: ['/api/products/1', '/api/products/2']),
        ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'references'),
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    protected Collection $items;
}