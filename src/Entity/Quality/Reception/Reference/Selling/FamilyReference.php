<?php

namespace App\Entity\Quality\Reception\Reference\Selling;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Project\Product\Family;
use App\Entity\Quality\Reception\Reference\Reference;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template-extends Reference<Family>
 */
#[
    ApiResource(
        description: 'Définition d\'un contrôle réception Famille de Produit',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un contrôle réception pour une famille de Produit',
                    'summary' => 'Créer un contrôle réception pour une famille de produit',
                    'tags' => ['Reference']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un contrôle réception pour une famille de Produit',
                    'summary' => 'Récupère un contrôle réception pour une famille de Produit',
                    'tags' => ['Reference']
                ],
            ]
        ],
        shortName: 'ProductFamilyReference',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_QUALITY_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:reference'],
            'openapi_definition_name' => 'ProductFamilyReference-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:reference'],
            'openapi_definition_name' => 'ProductFamilyReference-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'product_family_reference')
]
class FamilyReference extends Reference {
    #[
        ApiProperty(description: 'Familles', readableLink: false, example: ['/api/product-families/1', '/api/product-families/2']),
        ORM\JoinTable(name: 'product_family_reference_product_family'),
        ORM\ManyToMany(targetEntity: Family::class, inversedBy: 'references'),
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    protected Collection $items;
}
