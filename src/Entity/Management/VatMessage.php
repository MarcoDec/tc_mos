<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['name']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        description: 'Message TVA',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les messages TVA',
                    'summary' => 'Récupère les messages TVA',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un message TVA',
                    'summary' => 'Créer un message TVA',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un message TVA',
                    'summary' => 'Supprime un message TVA',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un message TVA',
                    'summary' => 'Modifie un message TVA',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name'],
            'openapi_definition_name' => 'VatMessage-write'
        ],
        normalizationContext: [
            'groups' => ['read:name', 'read:id'],
            'openapi_definition_name' => 'VatMessage-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table,
    UniqueEntity('name')
]
class VatMessage extends Entity {
    final public const FORCE_CHOICES = [
        'TVA par défaut selon le pays du client' => self::FORCE_DEFAULT,
        'Force SANS TVA' => self::FORCE_WITH,
        'Force AVEC TVA' => self::FORCE_WITHOUT,
    ];
    final public const FORCE_DEFAULT = 0;
    final public const FORCE_WITH = 1;
    final public const FORCE_WITHOUT = 2;

    #[
        ApiProperty(description: 'Message', required: true, example: "Ventes intra-communautaire :\u{a0}Exonération de TVA article 262 TERI\u{a0}du CGI."),
        Assert\NotBlank,
        ORM\Column(length: 120),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    private ?string $name = null;

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
