<?php

namespace App\Entity\Production\Company;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Interfaces\CompanyInterface;
use App\Entity\Management\Society\Company\Company;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'company'=> 'exact']),
    ApiFilter(filterClass: OrderFilter::class, properties: ['name']),
    ApiResource(
        description: 'Zone',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les zones',
                    'summary' => 'Récupère les zones'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\') or is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une zone',
                    'summary' => 'Créer une zone'
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
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une zone',
                    'summary' => 'Modifie une zone'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:zone'],
            'openapi_definition_name' => 'CompanySupply-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:zone'],
            'openapi_definition_name' => 'CompanySupply-read',
            'skip_null_values' => false
        ],
        paginationClientEnabled: true
    ),
    ORM\Entity
]
class Zone extends Entity {
    #[
        ApiProperty(description: 'Compagnie', readableLink: false),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['read:zone', 'read:engine', 'write:zone','read:engine-maintenance-event'])
    ]
    private ?Company $company = null;

    #[
        ApiProperty(description: 'Nom', example: 'Zone sertissage'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:zone', 'read:engine', 'write:zone','read:engine-maintenance-event'])
    ]
    private ?string $name = null;

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
