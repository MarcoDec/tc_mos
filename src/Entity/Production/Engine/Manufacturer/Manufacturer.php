<?php

namespace App\Entity\Production\Engine\Manufacturer;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Society\Society;
use App\Entity\Traits\NameTrait;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'name' => 'partial'
    ]),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'society' => 'name',
    ]),
    ApiFilter(OrderFilter::class, properties: [
        'name',
    ]),
    ApiResource(
        description: 'Fabricant',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les fabricants',
                    'summary' => 'Récupère les fabricants',
                ],
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un fabricant',
                    'summary' => 'Créer un fabricant',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un fabricant',
                    'summary' => 'Supprime un fabricant',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un fabricant',
                    'summary' => 'Récupère un fabricant'
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un fabricant',
                    'summary' => 'Modifie un fabricant',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name', 'write:manufacturer'],
            'openapi_definition_name' => 'Manufacturer-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:manufacturer'],
            'openapi_definition_name' => 'Manufacturer-read'
        ],
    ),
    ORM\Entity
]
class Manufacturer extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Peugeot'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Société', required: false, readableLink: false, example: '/api/societies/2'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Society::class),
        Serializer\Groups(['read:manufacturer', 'write:manufacturer'])
    ]
    private ?Society $society;

    public function getSociety(): ?Society {
        return $this->society;
    }

    public function setSociety(?Society $society): self {
        $this->society = $society;

        return $this;
    }
}
