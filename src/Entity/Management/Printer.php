<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Management\PrinterColorType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Society\Company\Company;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'name' => 'partial',
        'ip' => 'partial',
        'maxLabelWidth' => 'partial',
        'maxLabelHeight' => 'partial'
    ]),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'company'
    ]),
    ApiFilter(filterClass: OrderFilter::class, properties: [
        'name',
        'ip',
        'color',
        'maxLabelWidth',
        'maxLabelHeight'
    ]),
    ApiResource(
        description: 'Imprimante',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les imprimantes',
                    'summary' => 'Récupère les imprimantes',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\') or is_granted(\''.Roles::ROLE_MANAGEMENT_READER.'\')'
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une imprimante',
                    'summary' => 'Créer une imprimante',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')'
            ]
        ],
        itemOperations: ['get','patch', 'delete'],
        denormalizationContext: ['groups' => ['write:printer']],
        normalizationContext: [
            'groups' => ['read:id', 'read:printer'],
            'openapi_definition_name' => 'Printer-read'
        ],
        paginationEnabled: true
    ),
    ORM\Entity()
]/*repositoryClass: PrinterRepository::class, readOnly: true*/
class Printer extends Entity {
    #[
        ApiProperty(description: 'Couleur', openapiContext: ['enum' => PrinterColorType::TYPES]),
        ORM\Column(type: 'printer_color', options: ['default' => PrinterColorType::TYPE_GREEN]),
        Serializer\Groups(['read:printer', 'write:printer'])
    ]
    private ?string $color = PrinterColorType::TYPE_GREEN;

    #[
        ApiProperty(description: 'Company', example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:printer', 'write:printer'])
    ]
    private ?Company $company;

    #[
        ApiProperty(description: 'IP', example: '192.168.2.115'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:printer', 'write:printer'])
    ]
    private ?string $ip = null;

    #[
        ApiProperty(description: 'Nom', example: 'zpl'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:printer', 'write:printer'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Largeur maximale des étiquettes en inch', example: '4.0'),
        ORM\Column(type: 'float', nullable: true),
        Serializer\Groups(['read:printer', 'write:printer'])
    ]
    private ?float $maxLabelWidth = 0; // in inches
    #[
        ApiProperty(description: 'Hauteur maximale des étiquettes en inch', example: '6.0'),
        ORM\Column(type: 'float', nullable: true),
        Serializer\Groups(['read:printer', 'write:printer'])
    ]
    private ?float $maxLabelHeight = 0; // in inches

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

    /**
     * @return ?float
     */
    public function getMaxLabelWidth(): ?float
    {
        return $this->maxLabelWidth;
    }

    /**
     * @param ?float $maxLabelWidth
     * @return Printer
     */
    public function setMaxLabelWidth(?float $maxLabelWidth): Printer
    {
        $this->maxLabelWidth = $maxLabelWidth;
        return $this;
    }

    /**
     * @return ?float
     */
    public function getMaxLabelHeight(): ?float
    {
        return $this->maxLabelHeight;
    }

    /**
     * @param ?float $maxLabelHeight
     * @return Printer
     */
    public function setMaxLabelHeight(?float $maxLabelHeight): Printer
    {
        $this->maxLabelHeight = $maxLabelHeight;
        return $this;
    }

}
