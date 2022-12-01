<?php

declare(strict_types=1);

namespace App\Entity\Quality\Reject;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\Type\Hr\Employee\Role;
use App\Entity\Entity;
use App\Filter\OrderFilter;
use App\Filter\SearchFilter;
use App\State\PersistProcessor;
use App\State\RemoveProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['name']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        shortName: 'RejectType',
        description: 'Type de rebus',
        operations: [
            new GetCollection(openapiContext: ['description' => 'Récupère les types', 'summary' => 'Récupère les types']),
            new Post(
                openapiContext: ['description' => 'Créer un type', 'summary' => 'Créer un type'],
                processor: PersistProcessor::class
            ),
            new Delete(
                openapiContext: ['description' => 'Supprime un type', 'summary' => 'Supprime un type'],
                processor: RemoveProcessor::class
            ),
            new Patch(
                inputFormats: ['json' => ['application/merge-patch+json']],
                openapiContext: ['description' => 'Modifie un type', 'summary' => 'Modifie un type'],
                processor: PersistProcessor::class
            )
        ],
        inputFormats: 'json',
        outputFormats: 'jsonld',
        normalizationContext: [
            'groups' => ['id', 'reject-type-read'],
            'skip_null_values' => false,
            'openapi_definition_name' => 'reject-type-read'
        ],
        denormalizationContext: ['groups' => ['reject-type-write']],
        order: ['name' => 'asc'],
        security: Role::GRANTED_QUALITY_ADMIN
    ),
    ORM\Entity,
    ORM\Table(name: 'reject_type'),
    UniqueEntity(fields: ['name', 'deleted'], ignoreNull: false)
]
class Type extends Entity {
    #[
        ApiProperty(description: 'Nom', example: 'Dimensions'),
        Assert\Length(min: 3, max: 40),
        Assert\NotBlank,
        ORM\Column(length: 40),
        Serializer\Groups(['quality-type-read', 'quality-type-write'])
    ]
    private ?string $name = null;

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
