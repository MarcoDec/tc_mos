<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Action\PlaceholderAction;
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
            ],
            'select-options' => [
                'controller' => PlaceholderAction::class,
                'filters' => [],
                'method' => 'GET',
                'normalization_context' => [
                    'groups' => ['read:vat-message:options', 'read:id'],
                    'openapi_definition_name' => 'VatMessage-options',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les messages TVA pour les select',
                    'summary' => 'Récupère les messages TVA pour les select',
                ],
                'pagination_enabled' => false,
                'path' => '/vat-messages/options'
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
            'groups' => ['write:vat-message'],
            'openapi_definition_name' => 'VatMessage-write'
        ],
        normalizationContext: [
            'groups' => ['read:vat-message', 'read:id'],
            'openapi_definition_name' => 'VatMessage-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    UniqueEntity('name')
]
class VatMessage extends Entity {
    #[
        ApiProperty(description: 'Message', example: "Ventes intra-communautaire :\u{a0}Exonération de TVA article 262 TERI\u{a0}du CGI."),
        Assert\NotBlank,
        ORM\Column(length: 120),
        Serializer\Groups(['read:vat-message', 'read:vat-message:options', 'write:vat-message'])
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
