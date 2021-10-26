<?php

namespace App\Entity\Quality\Reject;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        description: 'QualityReject',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les qualités du reject',
                    'summary' => 'Récupère les qualités du reject',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une qualité du reject',
                    'summary' => 'Créer une qualité du reject',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une qualité du reject',
                    'summary' => 'Supprime une qualité du reject',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une qualité du reject',
                    'summary' => 'Modifie une qualité du reject',
                ]
            ]
        ],
        shortName: 'QualiteReject',
        attributes: [
            'security' => 'is_granted(\'' . Roles::ROLE_QUALITY_ADMIN . '\')'
        ],
        denormalizationContext: [
            'groups' => ['write:qualite', 'write:name'],
            'openapi_definition_name' => 'QualiteReject-write'
        ],
        normalizationContext: [
            'groups' => ['read:qualite', 'read:id', 'read:name'],
            'openapi_definition_name' => 'QualiteReject-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(),
    ORM\Table(name: 'reject_type')
]
class Type extends Entity
{
    #[
        ApiProperty(description: 'Nom', required: true, example: 'sertissage dimensionnelle'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
