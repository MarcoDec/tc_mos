<?php

namespace App\Entity\Project\Operation;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Purchase\Component\Family;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['assembly']),
    ApiFilter(filterClass: OrderFilter::class, properties: ['name']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        description: 'Type d\'opération',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les types de d\'opération',
                    'summary' => 'Récupère les types de d\'opération',
                ]
            ],
            'options' => [
                'controller' => PlaceholderAction::class,
                'filters' => [],
                'method' => 'GET',
                'normalization_context' => [
                    'groups' => ['read:id', 'read:type:option'],
                    'openapi_definition_name' => 'OperationType-options',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les types pour les select',
                    'summary' => 'Récupère les types pour les select',
                ],
                'order' => ['name' => 'asc'],
                'pagination_enabled' => false,
                'path' => '/operation-types/options'
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Crée un type d\'opération',
                    'summary' => 'Crée un type d\'opération',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un type d\'opération',
                    'summary' => 'Supprime un type d\'opération',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un type d\'opération',
                    'summary' => 'Modifie un type d\'opération',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')'
            ],
        ],
        shortName: 'OperationType',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PROJECT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:operation-type'],
            'openapi_definition_name' => 'OperationType-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:operation-type'],
            'openapi_definition_name' => 'OperationType-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'operation_type'),
    UniqueEntity(['name'])
]
class Type extends Entity {
    #[
        ApiProperty(description: 'Assemblage', example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:operation-type', 'write:operation-type'])
    ]
    private bool $assembly = false;

    /** @var Collection<int, Family> */
    #[
        ApiProperty(description: 'Familles de composant', readableLink: false, example: ['/api/component-families/5', '/api/component-families/12']),
        ORM\JoinTable(name: 'operation_type_component_family'),
        ORM\ManyToMany(targetEntity: Family::class),
        Serializer\Groups(['read:operation-type', 'write:operation-type'])
    ]
    private Collection $families;

    #[
        ApiProperty(description: 'Nom'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:operation-type', 'write:operation-type'])
    ]
    private ?string $name = null;

    public function __construct() {
        $this->families = new ArrayCollection();
    }

    final public function addFamily(Family $family): self {
        if (!$this->families->contains($family)) {
            $this->families->add($family);
        }
        return $this;
    }

    /**
     * @return Collection<int, Family>
     */
    final public function getFamilies(): Collection {
        return $this->families;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    #[Serializer\Groups(['read:type:option'])]
    final public function getText(): ?string {
        return $this->getName();
    }

    final public function isAssembly(): bool {
        return $this->assembly;
    }

    final public function removeFamily(Family $family): self {
        if ($this->families->contains($family)) {
            $this->families->removeElement($family);
        }
        return $this;
    }

    final public function setAssembly(bool $assembly): self {
        $this->assembly = $assembly;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
