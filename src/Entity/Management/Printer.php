<?php

namespace App\Entity\Management;

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
        'name' => 'partial'
    ]),
    ApiFilter(filterClass: OrderFilter::class, properties: [
        'name',
    ]),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'company' => 'name',
    ]),
    ApiResource(
        description: 'Imprimante',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les imprimantes',
                    'summary' => 'Récupère les imprimantes',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_READER.'\')'
            ]
        ],
        itemOperations: [
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:printer'],
            'openapi_definition_name' => 'Printer-read'
        ],
    ),
    ORM\Entity(readOnly: true)
]
class Printer extends Entity {
    use CompanyTrait;
    use NameTrait;

    #[
        ApiProperty(description: 'Company', required: false, example: '/api/companies/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Company::class),
        Serializer\Groups(['read:company'])
    ]
    protected ?Company $company;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Officejet 2022'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'IP', required: true, example: 'Officejet 2022'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Assert\Ip,
        Serializer\Groups(['read:printer'])
    ]
    private ?string $ip;

    public function getIp(): ?string {
        return $this->ip;
    }

    public function setIp(?string $ip): self {
        $this->ip = $ip;

        return $this;
    }
}
