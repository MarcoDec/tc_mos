<?php

namespace App\Entity\Production\Engine\Manufacturer;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Society\Society;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['name', 'society.name']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['society']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        description: 'Fabricant',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les fabricants',
                    'summary' => 'Récupère les fabricants',
                ],
            ],
            'options' => [
                'controller' => PlaceholderAction::class,
                'filters' => [],
                'method' => 'GET',
                'normalization_context' => [
                    'groups' => ['read:id', 'read:manufacturer:option'],
                    'openapi_definition_name' => 'Manufacturer-options',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les fabriquants pour les select',
                    'summary' => 'Récupère les fabriquants pour les select',
                ],
                'order' => ['name' => 'asc'],
                'pagination_enabled' => false,
                'path' => '/manufacturers/options'
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
            'get' => NO_ITEM_GET_OPERATION,
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
            'groups' => ['write:manufacturer'],
            'openapi_definition_name' => 'Manufacturer-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:manufacturer'],
            'openapi_definition_name' => 'Manufacturer-read',
            'skip_null_values' => false
        ],
        paginationEnabled: false
    ),
    ORM\Entity
]
class Manufacturer extends Entity {
    #[
        ApiProperty(description: 'Nom', required: true, example: 'Peugeot'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:manufacturer', 'write:manufacturer', 'read:manufacturer-engine'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Société', readableLink: false, required: false, example: '/api/societies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:manufacturer', 'write:manufacturer'])
    ]
    private ?Society $society;

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getSociety(): ?Society {
        return $this->society;
    }

    #[Serializer\Groups(['read:manufacturer:option'])]
    final public function getText(): ?string {
        return $this->getName();
    }
    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setSociety(?Society $society): self {
        $this->society = $society;
        return $this;
    }
}
