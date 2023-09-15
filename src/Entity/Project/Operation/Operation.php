<?php

namespace App\Entity\Project\Operation;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Entity\Production\Manufacturing\Operation as ManufacturingOperation;
use App\Filter\RelationFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['auto']),
    ApiFilter(filterClass: OrderFilter::class, properties: ['code', 'name', 'type.name']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['type']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['boundary' => 'partial', 'code' => 'partial', 'name' => 'partial']),
    ApiResource(
        description: 'Opération',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les opérations',
                    'summary' => 'Récupère les opérations',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Crée une opération',
                    'summary' => 'Crée une opération',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une opération',
                    'summary' => 'Supprime une opération',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une opération',
                    'summary' => 'Modifie une opération',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')'
            ],
        ],
        shortName: 'ProjectOperation',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PROJECT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:project-operation'],
            'openapi_definition_name' => 'ProjectOperation-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:project-operation'],
            'openapi_definition_name' => 'ProjectOperation-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'project_operation')
]
class Operation extends Entity implements MeasuredInterface {
    #[
        ApiProperty(description: 'Automatique', example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:project-operation', 'write:project-operation', 'read:manufacturing-operation'])
    ]
    private bool $auto = false;

    #[
        ApiProperty(description: 'Limite', example: 'Lorem ipsum'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:project-operation', 'write:project-operation', 'read:manufacturing-operation'])
    ]
    private ?string $boundary = null;

    #[
        ApiProperty(description: 'Cadence', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:project-operation', 'write:project-operation', 'read:manufacturing-operation'])
    ]
    private Measure $cadence;

    #[
        ApiProperty(description: 'Code', example: 'SAZ'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:project-operation', 'write:project-operation', 'read:manufacturing-operation'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Nom', example: 'Nom'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:project-operation', 'write:project-operation', 'read:manufacturing-operation'])
    ]
    private ?string $name = null;

    /** @var Collection<int, ManufacturingOperation> */
    #[ORM\OneToMany(mappedBy: 'operation', targetEntity: ManufacturingOperation::class, fetch: 'EXTRA_LAZY')]
    private Collection $operations;

    #[
        ApiProperty(description: 'Prix', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:project-operation', 'write:project-operation'])
    ]
    private Measure $price;

    #[
        ApiProperty(description: 'Durée', openapiContext: ['$ref' => '#/components/schemas/Measure-duration']),
        ORM\Embedded,
        Serializer\Groups(['read:project-operation', 'write:project-operation', 'read:manufacturing-operation'])
    ]
    private Measure $time;

    #[
        ApiProperty(description: 'Type', readableLink: false, required: false, example: '/api/operation-types/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:project-operation', 'write:project-operation', 'read:manufacturing-operation'])
    ]
    private ?Type $type = null;

    public function __construct() {
        $this->cadence = new Measure();
        $this->operations = new ArrayCollection();
        $this->price = new Measure();
        $this->time = new Measure();
    }

    final public function addOperation(ManufacturingOperation $operation): self {
        if (!$this->operations->contains($operation)) {
            $this->operations->add($operation);
            $operation->setOperation($this);
        }
        return $this;
    }

    final public function getBoundary(): ?string {
        return $this->boundary;
    }

    final public function getCadence(): Measure {
        return $this->cadence;
    }

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getCountOperations(): int {
        return $this->operations->count();
    }

    final public function getName(): ?string {
        return $this->name;
    }

    /**
     * @return Collection<int, ManufacturingOperation>
     */
    final public function getOperations(): Collection {
        return $this->operations;
    }

    final public function getPrice(): Measure {
        return $this->price;
    }

    final public function getTime(): Measure {
        return $this->time;
    }

    final public function getType(): ?Type {
        return $this->type;
    }

    final public function isAuto(): bool {
        return $this->auto;
    }

    final public function removeOperation(ManufacturingOperation $operation): self {
        if ($this->operations->contains($operation)) {
            $this->operations->removeElement($operation);
            if ($operation->getOperation() === $this) {
                $operation->setOperation(null);
            }
        }
        return $this;
    }

    final public function setAuto(bool $auto): self {
        $this->auto = $auto;
        return $this;
    }

    final public function setBoundary(?string $boundary): self {
        $this->boundary = $boundary;
        return $this;
    }

    final public function setCadence(Measure $cadence): self {
        $this->cadence = $cadence;
        return $this;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setPrice(Measure $price): self {
        $this->price = $price;
        return $this;
    }

    final public function setTime(Measure $time): self {
        $this->time = $time;
        return $this;
    }

    final public function setType(?Type $type): self {
        $this->type = $type;
        return $this;
    }

    public function getMeasures(): array
    {
        return [$this->cadence, $this->time, $this->price];
    }

    public function getUnit(): ?Unit
    {
        return null;
    }
}
