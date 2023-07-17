<?php

namespace App\Entity\Production\Quality;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Logistics\Stock\ComponentStock;
use App\Entity\Management\Unit;
use App\Entity\Production\Manufacturing\Operation;
use App\Entity\Purchase\Component\Component;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ApiResource(
        description: 'Contrôle Qualité Production',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les valeurs détaillées des Contrôles Qualité de production',
                    'summary' => 'Récupère les valeurs détaillées des Contrôles Qualité de productions',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Ajoute les valeurs détaillées des Contrôle Qualité de production',
                    'summary' => 'Ajoute les valeurs détaillées des Contrôle Qualité de production'
                ]
            ]
        ],
        itemOperations: [
            'get' => NO_ITEM_GET_OPERATION,
            'patch',
            'delete'
        ],
        shortName: 'ProductionQualityValue',
        denormalizationContext: [
            'groups' => ['write:production-quality-value', 'write:measure'],
            'openapi_definition_name' => 'ProductionQualityValue-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:production-quality-value', 'read:measure'],
            'openapi_definition_name' => 'ProductionQualityValue-read'
        ],
        paginationClientEnabled: true
    ),
    \Doctrine\ORM\Mapping\Entity
]
class ProductionQualityValue extends Entity implements MeasuredInterface
{
     #[
         ApiProperty(description: 'Composant Testé', example: '/api/components/1'),
         ManyToOne(targetEntity: Component::class),
         Groups(['read:production-quality-value', 'write:production-quality-value'])

     ]
    private Component $component;

    #[
        ApiProperty(description: 'Stock Composant utilisé', example: '/api/component-stocks/1'),
        ManyToOne(targetEntity: ComponentStock::class),
        Groups(['read:production-quality-value', 'write:production-quality-value'])
    ]
    private ComponentStock $componentStock;

     #[
         ApiProperty(description: 'Date du contrôle qualité', example: "2023-05-31"),
         Column(nullable: true, type: 'datetime'),
         Groups(['read:production-quality-value', 'write:production-quality-value'])
     ]
    private $dateQuality;

     #[
         ApiProperty(description: 'Hauteur mesurée', openapiContext: ['$ref' => '#/components/schemas/Measure-length']),
         Embedded,
         Groups(['read:production-quality-value', 'write:production-quality-value'])
     ]
    private Measure $hauteur;

    #[
        ApiProperty(description: 'Largeur mesurée', openapiContext: ['$ref' => '#/components/schemas/Measure-length']),
        Embedded,
        Groups(['read:production-quality-value', 'write:production-quality-value'])
    ]
    private Measure $largeur;

     #[
         ApiProperty(description: 'Matricule de l\'opérateur', example: '1224343'),
         Column(type: 'integer'),
         Groups(['read:production-quality-value', 'write:production-quality-value'])
     ]
    private $matriculeQualite;

     #[
         ApiProperty(description: 'Operation de production liée au contrôle', example: '/api/manufacturing-operations/1'),
         ManyToOne(targetEntity: Operation::class),
         Groups(['read:production-quality-value', 'write:production-quality-value'])
     ]
    private Operation $productionOperation;

    #[
        ApiProperty(description: 'Section mesurée', openapiContext: ['$ref' => '#/components/schemas/Measure-section']),
        Embedded,
        Groups(['read:production-quality-value', 'write:production-quality-value'])
    ]
    private Measure $section;

    #[
        ApiProperty(description: 'Effort à la traction mesuré', openapiContext: ['$ref' => '#/components/schemas/Measure-strength']),
        Embedded,
        Groups(['read:production-quality-value', 'write:production-quality-value'])
    ]
    private Measure $traction;

    public function __construct() {
       $this->traction = new Measure();
       $this->section = new Measure();
       $this->hauteur = new Measure();
       $this->largeur = new Measure();
    }

    public function getComponent(): ?Component {
        return $this->component;
    }

    public function getDateQuality(): ?DateTimeInterface {
        return $this->dateQuality;
    }

    public function getMatriculeQualite(): ?int {
        return $this->matriculeQualite;
    }

    public function getOperation(): ?ProductionOperation {
        return $this->productionOperation;
    }

    public function getProductionOperation(): ?ProductionOperation {
        return $this->productionOperation;
    }

    public function getTitle(): string {
        return 'ProductionQualityValue'.$this->getId();
    }

    public function setComponent(Component $component): self {
        $this->component = $component;
        return $this;
    }

    public function setDateQuality(DateTimeInterface $dateQuality): self {
        $this->dateQuality = $dateQuality;
        return $this;
    }

    public function setMatriculeQualite(int $matriculeQualite): self {
        $this->matriculeQualite = $matriculeQualite;
        return $this;
    }

    public function setProductionOperation(ProductionOperation $productionOperation): self {
        $this->productionOperation = $productionOperation;
        return $this;
    }

    public function getComponentStock(): ComponentStock
    {
        return $this->componentStock;
    }

    public function setComponentStock(ComponentStock $componentStock): self
    {
        $this->componentStock = $componentStock;
        return $this;
    }

    public function getHauteur(): Measure
    {
        return $this->hauteur;
    }

    public function setHauteur(Measure $hauteur): self
    {
        $this->hauteur = $hauteur;
        return $this;
    }

    public function getLargeur(): Measure
    {
        return $this->largeur;
    }

    public function setLargeur(Measure $largeur): self
    {
        $this->largeur = $largeur;
        return $this;
    }

    public function getSection(): Measure
    {
        return $this->section;
    }

    public function setSection(Measure $section): self
    {
        $this->section = $section;
        return $this;
    }

    public function getTraction(): Measure
    {
        return $this->traction;
    }

    public function setTraction(Measure $traction): self
    {
        $this->traction = $traction;
        return $this;
    }

    public function getMeasures(): array
    {
        return [$this->traction, $this->largeur, $this->hauteur, $this->section];
    }

    public function getUnit(): ?Unit
    {
        return $this->component->getUnit();
    }
}
