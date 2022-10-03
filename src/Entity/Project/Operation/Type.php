<?php

namespace App\Entity\Project\Operation;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
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
    ApiResource(
        description: 'Type d\'opération',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les types de d\'opération',
                    'summary' => 'Récupère les types de d\'opération',
                ]
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
        ApiProperty(description: 'Famille de produit', readableLink: false, example: ['/api/component-families/5', '/api/component-families/12']),
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
