<?php

namespace App\Entity\Quality\Reception\Reference\Management;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Quality\Reception\Reference\Reference;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template-extends Reference<Company>
 */
#[
    ApiResource(
        description: 'Définition d\'un contrôle réception pour une compagnie',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un contrôle réception pour une compagnie',
                    'summary' => 'Créer un contrôle réception pour une compagnie',
                    'tags' => ['Reference']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un contrôle réception pour une compagnie',
                    'summary' => 'Récupère un contrôle réception pour une compagnie',
                    'tags' => ['Reference']
                ],
            ]],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_QUALITY_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:reference'],
            'openapi_definition_name' => 'CompanyReference-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:reference'],
            'openapi_definition_name' => 'CompanyReference-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class CompanyReference extends Reference {
    #[
        ApiProperty(description: 'Composants', readableLink: false, example: ['/api/companies/1', '/api/companies/2']),
        ORM\ManyToMany(targetEntity: Company::class, inversedBy: 'references'),
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    protected Collection $items;
}
