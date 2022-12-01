<?php

declare(strict_types=1);

namespace App\Entity\Production\Engine;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\Type\Hr\Employee\Role;
use App\Doctrine\Type\Production\Engine\EngineType;
use App\Dto\Production\Engine\GroupGenerator;
use App\Entity\Entity;
use App\Filter\SearchFilter;
use App\State\PersistProcessor;
use App\State\RemoveProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['safetyDevice']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['code' => 'partial', 'name' => 'partial', 'type' => 'exact']),
    ApiResource(
        shortName: 'EngineGroup',
        description: 'Groupe d\'équipement',
        operations: [
            new GetCollection(
                openapiContext: ['description' => 'Récupère les groupes', 'summary' => 'Récupère les groupes']
            ),
            new Post(
                openapiContext: ['description' => 'Créer un groupe', 'summary' => 'Créer un groupe'],
                input: GroupGenerator::class,
                processor: PersistProcessor::class
            ),
            new Delete(
                openapiContext: ['description' => 'Supprime un groupe', 'summary' => 'Supprime un groupe'],
                processor: RemoveProcessor::class
            ),
            new Patch(
                inputFormats: ['json' => ['application/merge-patch+json']],
                openapiContext: ['description' => 'Modifie un groupe', 'summary' => 'Modifie un groupe'],
                denormalizationContext: ['groups' => ['engine-group-write']],
                processor: PersistProcessor::class
            )
        ],
        inputFormats: 'json',
        outputFormats: 'jsonld',
        normalizationContext: [
            'groups' => ['id', 'engine-group-read'],
            'skip_null_values' => false,
            'openapi_definition_name' => 'engine-group-read'
        ],
        order: ['code' => 'asc'],
        security: Role::GRANTED_MANAGEMENT_ADMIN
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'engine'),
    ORM\DiscriminatorMap(EngineType::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'engine_group'),
    UniqueEntity(fields: 'code', ignoreNull: false),
    UniqueEntity(fields: 'name', ignoreNull: false)
]
abstract class Group extends Entity {
    #[
        ApiProperty(description: 'Code', example: 'TA'),
        Assert\Length(min: 2, max: 3),
        Assert\NotBlank,
        ORM\Column(length: 3),
        Serializer\Groups(['engine-group-read', 'engine-group-write'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Name', example: 'Table d\'assemblage'),
        Assert\Length(min: 3, max: 35),
        Assert\NotBlank,
        ORM\Column(length: 35),
        Serializer\Groups(['engine-group-read', 'engine-group-write'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Organe sécurité', default: false, example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['engine-group-read', 'engine-group-write'])
    ]
    private bool $safetyDevice = false;

    public function getCode(): ?string {
        return $this->code;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function isSafetyDevice(): bool {
        return $this->safetyDevice;
    }

    public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    public function setSafetyDevice(bool $safetyDevice): self {
        $this->safetyDevice = $safetyDevice;
        return $this;
    }
}
