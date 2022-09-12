<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Management\PrinterColorType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\EntityId;
use App\Entity\Management\Society\Company\Company;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

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
        paginationEnabled: false
    ),
    ORM\Entity(readOnly: true)
]
class Printer extends EntityId {
    #[
        ApiProperty(description: 'Couleur', openapiContext: ['enum' => PrinterColorType::TYPES]),
        ORM\Column(type: 'printer_color', options: ['default' => PrinterColorType::TYPE_GREEN]),
        Serializer\Groups(['read:printer'])
    ]
    private ?string $color = PrinterColorType::TYPE_GREEN;

    #[
        ApiProperty(description: 'Company', required: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:printer'])
    ]
    private ?Company $company;

    #[
        ApiProperty(description: 'IP', required: true, example: '192.168.2.115'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:printer'])
    ]
    private ?string $ip = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'zpl'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:printer'])
    ]
    private ?string $name = null;

    final public function getColor(): ?string {
        return $this->color;
    }

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getIp(): ?string {
        return $this->ip;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setColor(?string $color): self {
        $this->color = $color;
        return $this;
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
