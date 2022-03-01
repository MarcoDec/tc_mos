<?php

namespace App\Doctrine\Entity\Quality\Reject;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Doctrine\Entity\Embeddable\Hr\Employee\Roles;
use App\Doctrine\Entity\Entity;
use App\Doctrine\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use const NO_ITEM_GET_OPERATION;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        description: 'Type de rebus',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les type de rebus',
                    'summary' => 'Récupère les type de rebus',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un type de rebus',
                    'summary' => 'Créer un type de rebus',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un type de rebus',
                    'summary' => 'Supprime un type de rebus',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un type de rebus',
                    'summary' => 'Modifie un type de rebus',
                ]
            ]
        ],
        shortName: 'RejectType',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_QUALITY_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name'],
            'openapi_definition_name' => 'RejectType-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name'],
            'openapi_definition_name' => 'RejectType-read'
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'reject_type')
]
class Type extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'sertissage dimensionnelle'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;
}
