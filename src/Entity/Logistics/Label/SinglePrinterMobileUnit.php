<?php

namespace App\Entity\Logistics\Label;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Logistics\Label\AddNewSinglePrinterMobileController;
use App\Controller\Logistics\Label\GetSinglePrinterMobileUnitFromHostController;
use App\Entity\Entity;
use App\Entity\Management\Printer;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Filter\RelationFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'name' => 'partial',
        'mobileUnitIp' => 'partial'
    ]),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'printer'
    ]),
    ApiResource(
        description: 'Poste mobile d\'impression d\'étiquette',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les postes mobile d\'impression d\'étiquette',
                    'summary' => 'Récupère les postes mobile d\'impression d\'étiquette',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un poste mobile d\'impression d\'étiquette',
                    'summary' => 'Créer un poste mobile d\'impression d\'étiquette',
                ]
            ],
            'addNewAndLink' => [
                'method' => 'POST',
                'path' => '/single-printer-mobile-units/addNewAndLink',
                'openapi_context' => [
                    'description' => 'Créer un poste mobile d\'impression d\'étiquette et l\'associe à une imprimante réseau',
                    'summary' => 'Créer un poste mobile d\'impression d\'étiquette et l\'associe à une imprimante réseau'
                ],
                'controller' => AddNewSinglePrinterMobileController::class
            ],
            'getFromHost' => [
                'method' => 'GET',
                'path' => '/single-printer-mobile-units/getFromHost',
                'openapi_context' => [
                    'description' => 'Récupère le poste mobile d\'impression d\'étiquette à partir de l\'adresse IP du poste',
                    'summary' => 'Récupère le poste mobile d\'impression d\'étiquette à partir de l\'adresse IP du poste'
                ],
                'controller' => GetSinglePrinterMobileUnitFromHostController::class
            ]
        ],
        itemOperations: ['get', 'patch', 'delete'],
        denormalizationContext: ['groups' => ['write:single-printer-mobile-unit']],
        normalizationContext: [
            'groups' => ['read:single-printer-mobile-unit', 'read:id'],
            'openapi_definition_name' => 'SinglePrinterMobileUnit-read'
        ]
    ),
    ORM\Entity()
    ]
class SinglePrinterMobileUnit extends Entity
{
    //Cette Classe définit des postes mobile d'impression d'étiquette. Chaque poste est associé à une imprimante.
    #[
        ApiProperty(description: 'Nom', example: 'Poste mobile 1'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:single-printer-mobile-unit', 'write:single-printer-mobile-unit'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Adresse IP du poste mobile', example: '192.168.2.5'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:single-printer-mobile-unit', 'write:single-printer-mobile-unit'])
    ]
    private ?string $mobileUnitIp = null;

    #[
        ApiProperty(description: 'Imprimante associé au poste mobile', example: '/api/printers/1'),
        ORM\ManyToOne(targetEntity: Printer::class),
        Serializer\Groups(['read:single-printer-mobile-unit', 'write:single-printer-mobile-unit'])
    ]
    private ?Printer $printer = null;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return SinglePrinterMobileUnit
     */
    public function setName(?string $name): SinglePrinterMobileUnit
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMobileUnitIp(): ?string
    {
        return $this->mobileUnitIp;
    }

    /**
     * @param string|null $mobileUnitIp
     * @return SinglePrinterMobileUnit
     */
    public function setMobileUnitIp(?string $mobileUnitIp): SinglePrinterMobileUnit
    {
        $this->mobileUnitIp = $mobileUnitIp;
        return $this;
    }

    /**
     * @return Printer|null
     */
    public function getPrinter(): ?Printer
    {
        return $this->printer;
    }

    /**
     * @param Printer|null $printer
     * @return SinglePrinterMobileUnit
     */
    public function setPrinter(?Printer $printer): SinglePrinterMobileUnit
    {
        $this->printer = $printer;
        return $this;
    }


}