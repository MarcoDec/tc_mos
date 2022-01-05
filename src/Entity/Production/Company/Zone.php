<?php

namespace App\Entity\Production\Company;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Society\Company;
use App\Entity\Traits\CompanyTrait;
use App\Entity\Traits\NameTrait;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'name' => 'partial',
    ]),
    ApiFilter(filterClass: OrderFilter::class, properties: [
        'name'
    ]),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'company' => 'name',
    ]),
    ApiResource(
        description: 'Zone',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les zones',
                    'summary' => 'Récupère les zones',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une zone',
                    'summary' => 'Créer une zone',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une zone',
                    'summary' => 'Supprime une zone',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une zone',
                    'summary' => 'Modifie une zone',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:company', 'write:name'],
            'openapi_definition_name' => 'CompanySupply-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:company', 'read:name'],
            'openapi_definition_name' => 'CompanySupply-read'
        ],
    ),
    ORM\Entity
]
class Zone extends Entity {
    use CompanyTrait;
    use NameTrait;

    #[
        ApiProperty(description: 'Company', required: false, example: '/api/companies/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Company::class),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    protected ?Company $company;

    #[
        ApiProperty(description: 'Nom', required: true),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;
}
