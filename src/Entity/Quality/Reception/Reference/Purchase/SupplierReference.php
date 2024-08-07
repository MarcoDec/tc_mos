<?php

namespace App\Entity\Quality\Reception\Reference\Purchase;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Purchase\Supplier\Supplier;
use App\Entity\Quality\Reception\Reference\Reference;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template-extends Reference<Supplier>
 */
#[
    ApiResource(
        description: 'Définition d\'un contrôle réception Fournisseur',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un contrôle réception pour tous les composants d\'un même fournisseur',
                    'summary' => 'Créer un contrôle réception pour tous les composants d\'un même fournisseur',
                    'tags' => ['Reference']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un contrôle réception pour tous les composant d\'un même fournisseur',
                    'summary' => 'Récupère un contrôle réception pour tous les composant d\'un même fournisseur',
                    'tags' => ['Reference']
                ],
            ]],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_QUALITY_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:reference'],
            'openapi_definition_name' => 'SupplierReference-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:reference'],
            'openapi_definition_name' => 'SupplierReference-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class SupplierReference extends Reference {
    #[
        ApiProperty(description: 'Fournisseurs', readableLink: false, example: ['/api/suppliers/1', '/api/suppliers/2']),
        ORM\ManyToMany(targetEntity: Supplier::class, inversedBy: 'references', fetch:'EAGER'),
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    protected Collection $items;
}
