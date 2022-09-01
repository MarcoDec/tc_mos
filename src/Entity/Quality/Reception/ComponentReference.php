<?php

namespace App\Entity\Quality\Reception;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Component\Family;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template-extends Reference<Family, Component>
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
class ComponentReference extends Reference {
    #[
        ApiProperty(description: 'Familles', readableLink: false, example: ['/api/component-families/1', '/api/component-families/2']),
        ORM\ManyToMany(targetEntity: Family::class),
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    protected Collection $families;

    #[
        ApiProperty(description: 'Composants', readableLink: false, example: ['/api/components/1', '/api/components/2']),
        ORM\ManyToMany(targetEntity: Component::class),
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    protected Collection $items;
}
