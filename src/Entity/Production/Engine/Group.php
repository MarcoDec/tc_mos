<?php

namespace App\Entity\Production\Engine;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Doctrine\DBAL\Types\Production\Engine\EngineType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Production\Engine\CounterPart\Group as CounterPartGroup;
use App\Entity\Production\Engine\Tool\Group as ToolGroup;
use App\Entity\Production\Engine\Workstation\Group as WorkstationGroup;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use App\Filter\CustomGetterFilter;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['safetyDevice']),
    ApiFilter(CustomGetterFilter::class, properties: ['getterFilter' => ['fields' => ['code', 'name']]]),
    ApiFilter(filterClass: OrderFilter::class, properties: ['code', 'name']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'code' => 'partial']),
    ApiResource(
        description: 'Groupe d\'équipement',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les groupes d\'équipement',
                    'summary' => 'Récupère les groupes d\'équipement',
                ]
            ],
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un groupe d\'équipement',
                    'summary' => 'Supprime un groupe d\'équipement',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un groupe d\'équipement',
                    'summary' => 'Modifie un groupe d\'équipement',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ]
        ],
        shortName: 'EngineGroup',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:engine-group'],
            'openapi_definition_name' => 'EngineGroup-write'
        ],
        normalizationContext: [
            'groups' => ['read:engine-group', 'read:id'],
            'openapi_definition_name' => 'EngineGroup-read',
            'skip_null_values' => false
        ],
        paginationEnabled: false
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'engine'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'engine_group')
]
abstract class Group extends Entity {
    final public const TYPES = [
        EngineType::TYPE_COUNTER_PART => CounterPartGroup::class,
        EngineType::TYPE_TOOL => ToolGroup::class,
        EngineType::TYPE_WORKSTATION => WorkstationGroup::class
    ];

    #[
        ApiProperty(description: 'Code ', required: true, example: 'TA'),
        Assert\Length(min: 2, max: 3),
        Assert\NotBlank,
        ORM\Column(length: 3),
        Serializer\Groups(['read:engine-group', 'write:engine-group'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Table d\'assemblage'),
        Assert\Length(min: 3, max: 35),
        Assert\NotBlank,
        ORM\Column(length: 35),
        Serializer\Groups(['read:engine-group', 'write:engine-group'])
    ]
    private ?string $name = null;

    #[
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:engine-group', 'write:engine-group'])
    ]
    private bool $safetyDevice = false;

    #[
        Serializer\Groups(['read:engine-group'])
    ]
    public function getType(): string {
        switch (get_class($this)) {
            case CounterPartGroup::class:
                return EngineType::TYPE_COUNTER_PART;
            case ToolGroup::class:
                return EngineType::TYPE_TOOL;
            case WorkstationGroup::class:
                return EngineType::TYPE_WORKSTATION;
            default:
                return '';
        }
    }


    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function isSafetyDevice(): bool {
        return $this->safetyDevice;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setSafetyDevice(bool $safetyDevice): self {
        $this->safetyDevice = $safetyDevice;
        return $this;
    }
    #[
        ApiProperty(description: 'Nom complet', example: 'MA-Machine'),
        Serializer\Groups(['read:engine-group', 'read:engine-group:collection'])
    ]
    public function getGetterFilter(): string {
        return $this->getCode().'-'.$this->getName();
    }
}
