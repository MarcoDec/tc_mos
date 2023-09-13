<?php

namespace App\Entity\Production\Quality;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Production\Manufacturing\Operation;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation as Serializer;
use DateTimeImmutable;

use App\Controller\Production\Quality\ItemProductionQualityComponentController;

#[
    ApiResource(
        description: 'Qualité Production',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les Contrôles Qualité de production',
                    'summary' => 'Récupère les Contrôles Qualité de productions',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Ajoute un Contrôle Qualité de production',
                    'summary' => 'Ajoute un Contrôle Qualité de production'
                ]
            ], 
            'filtreComponent' => [
                'controller' => ItemProductionQualityComponentController::class,
                'method' => 'GET',
                'openapi_context' => [
                    'description' => 'Filtrer par composant',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'api',
                        'schema' => [
                            'type' => 'integer',
                        ]
                    ]],
                    'summary' => 'Filtrer par composant'
                ],
                'path' => '/production-quality/componentFilter/{api}',
                'read' => false,
                'write' => false
            ]
        ],
        itemOperations: [
            'get' => NO_ITEM_GET_OPERATION,
            'patch',
            'delete'
        ],
        shortName: 'ProductionQuality',
        denormalizationContext: [
            'groups' => ['write:production-quality'],
            'openapi_definition_name' => 'ProductionQuality-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:production-quality'],
            'openapi_definition_name' => 'ProductionQuality-read'
        ],
        paginationEnabled: true
    ),
    \Doctrine\ORM\Mapping\Entity
]
class ProductionQuality extends Entity
{
    public const TYPE_LABELS = [
        0 => 'Non défini',
        1 => 'Controle OK',
        2 => 'Dérogation',
        3 => 'Bloqué',
    ];

     #[
         ApiProperty(description: "Commentaires"),
         Column(nullable: false, type: "text"),
         Serializer\Groups(['read:production-quality', 'write:production-quality'])
     ]
    private $comment;
     #[
         ApiProperty(description: 'Employé effectuant le contrôle', example: '/api/employees/1'),
         ManyToOne(targetEntity: Employee::class),
         Serializer\Groups(['read:production-quality', 'write:production-quality'])
     ]
    private $employee;
     #[
         ApiProperty(description: 'Nombre de contrôle effectué', example: '5'),
         Column(nullable: true, type: 'integer'),
         Serializer\Groups(['read:production-quality', 'write:production-quality'])
     ]
    private $numberOfControl;

     #[
         ApiProperty(description: 'Opération de production associée', example: '/api/manufacturing-operations/1'),
         ManyToOne(targetEntity: Operation::class),
         Serializer\Groups(['read:production-quality', 'write:production-quality'])
     ]
    private $productionOperation;

     #[
         ApiProperty(description: 'Date du contrôle', example: '2023-05-2022'),
         Column(nullable: true, type: 'datetime'),
         Serializer\Groups(['read:production-quality', 'write:production-quality'])
     ]
    private \DateTime $recordDate;

     #[
        ApiProperty(description: 'Type de résultat', example: '1'), //TODO: faire Enum
        Column(nullable: true, type: 'smallint'),
        Serializer\Groups(['read:production-quality', 'write:production-quality'])
     ]
    private $type;

    public function getComment(): ?string {
        return $this->comment;
    }

    public function getEmployee(): ?Employee {
        return $this->employee;
    }

    public function getNumberOfControl(): ?int {
        return $this->numberOfControl;
    }

    public function getProductionOperation(): ?Operation {
        return $this->productionOperation;
    }

    public function getRecordDate(): ?\DateTime {
        return $this->recordDate;
    }

    public function getTitle(): string {
        return $this->productionOperation ? 'Qualité de'.$this->productionOperation : 'Qualité de ???';
    }

    public function getType(): ?int {
        return $this->type;
    }

    public function getTypeLabel() {
        return self::TYPE_LABELS[$this->type];
    }

    public function setComment(string $comment): self {
        $this->comment = $comment;
        return $this;
    }

    public function setEmployee(?Employee $employee): self {
        $this->employee = $employee;
        return $this;
    }

    public function setNumberOfControl(int $numberOfControl): self {
        $this->numberOfControl = $numberOfControl;
        return $this;
    }

    public function setProductionOperation(?ProductionOperation $productionOperation): self {
        $this->productionOperation = $productionOperation;
        return $this;
    }

    public function setRecordDate(?\DateTime $recordDate): self {
        $this->recordDate = $recordDate;
        return $this;
    }

    public function setType(int $type): self {
        $this->type = $type;
        return $this;
    }
}
