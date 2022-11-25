<?php

declare(strict_types=1);

namespace App\Entity\Project\Operation;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\Type\Hr\Employee\Role;
use App\Entity\Entity;
use App\Entity\Purchase\Component\Family;
use App\Filter\SearchFilter;
use App\State\PersistProcessor;
use App\State\RemoveProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['assembly']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        shortName: 'OperationType',
        description: 'Type d\'opération',
        operations: [
            new GetCollection(openapiContext: [
                'description' => 'Récupère les types d\'opération',
                'summary' => 'Récupère les types d\'opération'
            ]),
            new Post(
                openapiContext: [
                    'description' => 'Créer un type d\'opération',
                    'summary' => 'Créer un type d\'opération'
                ],
                processor: PersistProcessor::class
            ),
            new Delete(
                openapiContext: [
                    'description' => 'Supprime un type d\'opération',
                    'summary' => 'Supprime un type d\'opération'
                ],
                processor: RemoveProcessor::class
            ),
            new Patch(
                inputFormats: ['json' => ['application/merge-patch+json']],
                openapiContext: [
                    'description' => 'Modifie un type d\'opération',
                    'summary' => 'Modifie un type d\'opération'
                ],
                processor: PersistProcessor::class
            )
        ],
        inputFormats: 'json',
        outputFormats: 'jsonld',
        normalizationContext: [
            'groups' => ['id', 'operation-type-read'],
            'skip_null_values' => false,
            'openapi_definition_name' => 'operation-type-read'
        ],
        denormalizationContext: ['groups' => ['operation-type-write']],
        order: ['name' => 'asc'],
        security: Role::GRANTED_PROJECT_ADMIN
    ),
    ORM\Entity,
    ORM\Table(name: 'operation_type'),
    UniqueEntity(fields: ['name', 'deleted'], ignoreNull: false)
]
class Type extends Entity {
    #[ORM\Column(options: ['default' => false])]
    private bool $assembly = false;

    /** @var Collection<int, Family> */
    #[
        ApiProperty(
            description: 'Familles de composants',
            readableLink: false,
            writableLink: false,
            example: ['/api/component-families/1', '/api/component-families/2']
        ),
        ORM\JoinTable(name: 'operation_type_component_family'),
        ORM\ManyToMany(targetEntity: Family::class),
        Serializer\Groups(['operation-type-read', 'operation-type-write'])
    ]
    private Collection $families;

    #[
        ApiProperty(description: 'Nom', example: 'Compactage'),
        Assert\Length(min: 3, max: 40),
        Assert\NotBlank,
        ORM\Column(length: 40),
        Serializer\Groups(['operation-type-read', 'operation-type-write'])
    ]
    private ?string $name = null;

    public function __construct() {
        $this->families = new ArrayCollection();
    }

    public function addFamily(Family $family): self {
        if ($this->families->contains($family) === false) {
            $this->families->add($family);
        }
        return $this;
    }

    /** @return Collection<int, Family> */
    public function getFamilies(): Collection {
        return $this->families;
    }

    public function getName(): ?string {
        return $this->name;
    }

    #[
        ApiProperty(description: 'Assemblage', required: true, default: false, example: false),
        Serializer\Groups('operation-type-read')
    ]
    public function isAssembly(): bool {
        return $this->assembly;
    }

    public function removeFamily(Family $family): self {
        if ($this->families->contains($family)) {
            $this->families->removeElement($family);
        }
        return $this;
    }

    #[
        ApiProperty(description: 'Assemblage', required: false, default: false, example: false),
        Serializer\Groups('operation-type-write')
    ]
    public function setAssembly(bool $assembly): self {
        $this->assembly = $assembly;
        return $this;
    }

    public function setDeleted(bool $deleted): Entity {
        parent::setDeleted($deleted);
        if ($this->isDeleted()) {
            $this->families = new ArrayCollection();
        }
        return $this;
    }

    public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
