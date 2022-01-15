<?php

namespace App\Entity\Project\Operation;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Purchase\Component\Family;
use App\Entity\Traits\NameTrait;
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
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un type d\'opération',
                    'summary' => 'Récupère un type d\'opération',
                ]
            ],
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
            'groups' => ['write:name', 'write:family', 'write:operation_type'],
            'openapi_definition_name' => 'ComponentFamily-write'
        ],
        normalizationContext: [
            'groups' => ['read:name', 'read:id', 'read:family', 'read:operation_type'],
            'openapi_definition_name' => 'ComponentFamily-read'
        ]
    ),
    ORM\Entity,
    ORM\Table(name: 'operation_type'),
    UniqueEntity(['name'])
]
class Type extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Nom', required: true),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Assemblage', required: false, example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:operation_type', 'write:operation_type'])
    ]
    private bool $assembly = false;

    /**
     * @var Collection<int, Family>
     */
    #[
        ApiProperty(description: 'Famille de produit', readableLink: false, example: ['/api/component-families/5', '/api/component-families/12']),
        ORM\ManyToMany(fetch: 'EXTRA_LAZY', targetEntity: Family::class, inversedBy: 'types'),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    private Collection $families;

    public function __construct() {
        $this->families = new ArrayCollection();
    }

    final public function addFamily(Family $family): self {
        if (!$this->families->contains($family)) {
            $this->families->add($family);
            $family->addType($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Family>
     */
    final public function getFamilies(): Collection {
        return $this->families;
    }

    final public function isAssembly(): bool {
        return $this->assembly;
    }

    final public function removeFamily(Family $family): self {
        if ($this->families->contains($family)) {
            $this->families->removeElement($family);
            $family->removeType($this);
        }
        return $this;
    }

    final public function setAssembly(bool $assembly): self {
        $this->assembly = $assembly;
        return $this;
    }
}
