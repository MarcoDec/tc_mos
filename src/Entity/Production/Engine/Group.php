<?php

namespace App\Entity\Production\Engine;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Production\Engine\CounterPart\Group as CounterPartGroup;
use App\Entity\Production\Engine\Tool\Group as ToolGroup;
use App\Entity\Production\Engine\Workstation\Group as WorkstationGroup;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['safetyDevice']),
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
            'groups' => ['write:engine-group', 'write:name'],
            'openapi_definition_name' => 'EngineGroup-write'
        ],
        normalizationContext: [
            'groups' => ['read:engine-group', 'read:id', 'read:name'],
            'openapi_definition_name' => 'EngineGroup-read',
            'skip_null_values' => false
        ]
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'engine_type'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'engine_group')
]
abstract class Group extends Entity {
    public const TYPES = [
        'counter-part' => CounterPartGroup::class,
        'tool' => ToolGroup::class,
        'workstation' => WorkstationGroup::class
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
        Serializer\Groups(['read:name', 'write:name'])
    ]
    private ?string $name = null;

    #[
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:engine-group', 'write:engine-group'])
    ]
    private bool $safetyDevice = false;

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
}
