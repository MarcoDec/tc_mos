<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Society\Company;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
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
        itemOperations: ['get' => NO_ITEM_GET_OPERATION],
        normalizationContext: [
            'groups' => ['read:id', 'read:printer'],
            'openapi_definition_name' => 'Printer-read'
        ],
    ),
    ORM\Entity(readOnly: true)
]
class Printer extends Entity {
    #[
        ApiProperty(description: 'Company', required: false, example: '/api/companies/1'),
        ORM\ManyToOne(fetch: 'EAGER'),
        Serializer\Groups(['read:printer'])
    ]
    protected ?Company $company;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Officejet 2022'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:printer'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'IP', required: true, example: 'Officejet 2022'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Assert\Ip,
        Serializer\Groups(['read:printer'])
    ]
    private ?string $ip = null;

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getIp(): ?string {
        return $this->ip;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setIp(?string $ip): self {
        $this->ip = $ip;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
