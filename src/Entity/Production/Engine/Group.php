<?php

namespace App\Entity\Production\Engine;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Production\Engine\Tool\Group as ToolGroup;
use App\Entity\Production\Engine\Workstation\Group as WorkstationGroup;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['safetyDevice']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'code' => 'partial']),
    ApiResource(
        description: 'EngineGroup',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les groupes',
                    'summary' => 'Récupère les groupes',
                ]
            ],
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un groupe',
                    'summary' => 'Supprime un groupe',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un groupe',
                    'summary' => 'Modifie un groupe',
                ]
            ]
        ],
        shortName: 'Group',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:group', 'write:group'],
            'openapi_definition_name' => 'Group-write'
        ],
        normalizationContext: [
            'groups' => ['read:group', 'read:id', 'read:name'],
            'openapi_definition_name' => 'Group-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(),
    ORM\DiscriminatorMap(Group::TYPES),
    ORM\DiscriminatorColumn(name: 'type', type: 'string'),
    ORM\InheritanceType('SINGLE_TABLE'),
    UniqueEntity('code'),
    UniqueEntity('name'),
    ORM\Table(name: 'engine_group')
]

abstract class Group extends Entity {
    use NameTrait;

    public const TYPES = ['tool' => ToolGroup::class, 'workstation' => WorkstationGroup::class];

    #[
        ApiProperty(description: 'Code ', required: true, example: 'MA'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:group', 'write:group'])
    ]
    private ?string $code = null;

    #[
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:group', 'write:group'])
    ]
    private bool $safetyDevice = false;

    public function getCode(): ?string {
        return $this->code;
    }

    public function getType(): string {
        return 'engine';
    }

    public function isSafetyDevice(): bool {
        return $this->safetyDevice;
    }

    public function setCode(?string $code): void {
        $this->code = $code;
    }

    public function setSafetyDevice(bool $safetyDevice): void {
        $this->safetyDevice = $safetyDevice;
    }
}
