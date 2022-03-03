<?php

namespace App\Entity\Production\Engine;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Production\Engine\CounterPart\Group as CounterPartGroup;
use App\Entity\Production\Engine\Tool\Group as ToolGroup;
use App\Entity\Production\Engine\Workstation\Group as WorkstationGroup;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['safetyDevice']),
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
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un groupe d\'équipement',
                    'summary' => 'Modifie un groupe d\'équipement',
                ]
            ]
        ],
        shortName: 'EngineGroup',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:engine-group', 'write:name'],
            'openapi_definition_name' => 'EngineGroup-write'
        ],
        normalizationContext: [
            'groups' => ['read:engine-group', 'read:id', 'read:name'],
            'openapi_definition_name' => 'EngineGroup-read'
        ]
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'string'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'engine_group')
]
abstract class Group extends Entity {
    use NameTrait;

    public const TYPES = [
        'counter-part' => CounterPartGroup::class,
        'tool' => ToolGroup::class,
        'workstation' => WorkstationGroup::class
    ];

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Table d\'assemblage'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Code ', required: true, example: 'TA'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:engine-group', 'write:engine-group'])
    ]
    private ?string $code = null;

    #[
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:engine-group', 'write:engine-group'])
    ]
    private bool $safetyDevice = false;

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function isSafetyDevice(): bool {
        return $this->safetyDevice;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setSafetyDevice(bool $safetyDevice): self {
        $this->safetyDevice = $safetyDevice;
        return $this;
    }
}
