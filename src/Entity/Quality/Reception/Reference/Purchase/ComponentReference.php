<?php

namespace App\Entity\Quality\Reception\Reference\Purchase;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Purchase\Component\Component;
use App\Entity\Quality\Reception\Reference\Reference;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @template-extends Reference<Component>
 */
#[   
    ApiFilter(filterClass: SearchFilter::class, properties: ['sampleQuantity' => 'partial']),
    ApiResource(
        description: 'Définition d\'un contrôle réception pour un composant',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un contrôle réception pour un composant',
                    'summary' => 'Créer un contrôle réception pour un composant',
                    'tags' => ['Reference']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un contrôle réception pour un composant',
                    'summary' => 'Récupère un contrôle réception pour un composant',
                    'tags' => ['Reference']
                ],
            ]],
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
class ComponentReference extends Reference {
    #[
        ApiProperty(description: 'Composants', readableLink: false, example: ['/api/components/1', '/api/components/2']),
        ORM\ManyToMany(targetEntity: Component::class, inversedBy: 'references'),
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    protected Collection $items;
}
