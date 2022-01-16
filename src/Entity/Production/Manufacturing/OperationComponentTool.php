<?php

namespace App\Entity\Production\Manufacturing;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Production\Engine\Tool\Tool;
use App\Entity\Purchase\Component\Component;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Opération de production d\'un composant et outil',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les opérations de production d\'un composant et outil',
                    'summary' => 'Récupère les opérations de production d\'un composant et outil',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Crée une opération de production d\'un composant et outil',
                    'summary' => 'Crée une opération de production d\'un composant et outil',
                ]
            ],
        ],
        itemOperations: [
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une opération de production d\'un composant et outil',
                    'summary' => 'Modifie une opération de production d\'un composant et outil',
                ]
            ],
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une opération de production d\'un composant et outil',
                    'summary' => 'Supprime une opération de production d\'un composant et outil',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:oct'],
            'openapi_definition_name' => 'OperationComponentTool-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:oct'],
            'openapi_definition_name' => 'OperationComponentTool-read'
        ],
    ),
    ORM\Entity
]
class OperationComponentTool extends Entity {
    #[
        ApiProperty(description: 'Numéro de lot', example: '165486543'),
        ORM\Column(nullable: true, type: 'string'),
        Serializer\Groups(['read:oct', 'write:oct'])
    ]
    private ?string $batchNumber = null;

    #[
        ApiProperty(description: 'Composant', required: false, example: '/api/components/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Component::class),
        Serializer\Groups(['read:oct', 'write:oct'])
    ]
    private ?Component $component;

    #[
        ApiProperty(description: 'Opération', required: false, example: '/api/operations/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Operation::class),
        Serializer\Groups(['read:oct', 'write:oct'])
    ]
    private ?Operation $operation;

    #[
        ApiProperty(description: 'Outil', required: false, example: '/api/tools/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Tool::class),
        Serializer\Groups(['read:oct', 'write:oct'])
    ]
    private ?Tool $tool;

    final public function getBatchNumber(): ?string {
        return $this->batchNumber;
    }

    final public function getComponent(): ?Component {
        return $this->component;
    }

    final public function getOperation(): ?Operation {
        return $this->operation;
    }

    final public function getTool(): ?Tool {
        return $this->tool;
    }

    final public function setBatchNumber(?string $batchNumber): self {
        $this->batchNumber = $batchNumber;
        return $this;
    }

    final public function setComponent(?Component $component): self {
        $this->component = $component;
        return $this;
    }

    final public function setOperation(?Operation $operation): self {
        $this->operation = $operation;
        return $this;
    }

    final public function setTool(?Tool $tool): self {
        $this->tool = $tool;
        return $this;
    }
}
