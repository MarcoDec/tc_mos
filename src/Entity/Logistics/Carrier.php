<?php

namespace App\Entity\Logistics;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Traits\AddressTrait;
use App\Entity\Traits\NameTrait;
use App\Filter\EnumFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: EnumFilter::class, id: 'country', properties: ['address.country']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiFilter(filterClass: SearchFilter::class, id: 'address', properties: Address::filter),
    ApiResource(
        description: 'Transporteur',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les transporteurs',
                    'summary' => 'Récupère les transporteurs',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un transporteur',
                    'summary' => 'Créer un transporteur',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un transporteur',
                    'summary' => 'Supprime un transporteur',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un transporteur',
                    'summary' => 'Modifie un transporteur',
                ]
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:address', 'write:name'],
            'openapi_definition_name' => 'Carrier-write'
        ],
        normalizationContext: [
            'groups' => ['read:address', 'read:id', 'read:name'],
            'openapi_definition_name' => 'Carrier-read'
        ]
    ),
    ORM\Entity
]
class Carrier extends Entity {
    use AddressTrait;
    use NameTrait;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'DHL'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;
}
