<?php

namespace App\Entity\Production\Quality;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Entity\Production\Manufacturing\Operation;
use App\Entity\Quality\Reject\Type;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ApiResource(
        description: 'Rejet Qualité de Production',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les rejets qualités de production',
                    'summary' => 'Récupère les rejets qualités de production',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Ajoute un rejet qualité de production',
                    'summary' => 'Ajoute un rejet qualité de production'
                ]
            ]
        ],
        itemOperations: [
            'get' => NO_ITEM_GET_OPERATION,
            'patch',
            'delete'
        ],
        shortName: 'ProductionQualityReject',
        denormalizationContext: [
            'groups' => ['write:production-quality-reject', 'write:measure'],
            'openapi_definition_name' => 'ProductionQualityReject-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:production-quality-reject', 'read:measure'],
            'openapi_definition_name' => 'ProductionQualityReject-read'
        ],
        paginationClientEnabled: true
    ),
    \Doctrine\ORM\Mapping\Entity
]
class ProductionQualityReject extends Entity implements MeasuredInterface
{
     #[
         ApiProperty(description: 'Opération de production concerné par le rejet qualité', readableLink: false,  example: '/api/production-operations/1'),
         ManyToOne(targetEntity: Operation::class),
         Groups(['write:production-quality-reject', 'read:production-quality-reject'])
     ]
    private Operation $productionOperation;

     #[
         ApiProperty(description: 'Type de rejet Qualité', readableLink: false, example: '/api/reject-types/1'),
         ManyToOne(targetEntity: Type::class),
         Groups(['write:production-quality-reject', 'read:production-quality-reject'])
     ]
    private Type $rejectType;

     #[
         ApiProperty(description: 'Contrôle Qualité opéré', readableLink: false, example: '/api/production-qualities/1'),
         ManyToOne(targetEntity: ProductionQuality::class),
         Groups(['write:production-quality-reject', 'read:production-quality-reject'])
     ]
    private ProductionQuality $qualityControl;

     #[
         ApiProperty(description: 'Quantité d\'élément concerné'),
         Embedded,
         Groups(['write:production-quality-reject', 'read:production-quality-reject'])
     ]
    private Measure $quantity;

    public function __construct() {
        $this->quantity = new Measure();
    }

    public function getProductionOperation(): ?Operation {
        return $this->productionOperation;
    }

    public function getRejectType(): ?Type {
        return $this->rejectType;
    }

    public function getQualityControl(): ?ProductionQuality {
        return $this->qualityControl;
    }

    public function getQuantity(): ?Measure {
        return $this->quantity;
    }

    public function getTitle(): string {
        return 'ProductionQualityReject'.$this->getId();
    }

    public function setProductionOperation(Operation $productionOperation): self {
        $this->productionOperation = $productionOperation;
        return $this;
    }

    public function setRejectType(Type $rejectType): self {
        $this->rejectType = $rejectType;
        return $this;
    }

    public function setQualityControl(ProductionQuality $qualityControl): self {
        $this->qualityControl = $qualityControl;
        return $this;
    }

    public function setQuantity(Measure $quantity): self {
        $this->quantity = $quantity;
        return $this;
    }

    public function getMeasures(): array
    {
        return [$this->quantity];
    }

    public function getUnit(): ?Unit
    {
        return $this->productionOperation->getOrder()->getProduct()->getUnit();
    }
}
