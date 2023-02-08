<?php

declare(strict_types=1);

namespace App\Entity\Logistics;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\Type\Hr\Employee\Role;
use App\Entity\Entity;
use App\Filter\SearchFilter;
use App\State\PersistProcessor;
use App\State\RemoveProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['code' => 'partial', 'name' => 'partial']),
    ApiResource(
        description: 'Incoterms',
        operations: [
            new GetCollection(
                openapiContext: ['description' => 'Récupère les incoterms', 'summary' => 'Récupère les incoterms'],
                security: Role::GRANTED_LOGISTICS_READER
            ),
            new Post(
                openapiContext: ['description' => 'Créer un incoterms', 'summary' => 'Créer un incoterms'],
                security: Role::GRANTED_LOGISTICS_WRITER,
                processor: PersistProcessor::class
            ),
            new Delete(
                openapiContext: ['description' => 'Supprime un incoterms', 'summary' => 'Supprime un incoterms'],
                security: Role::GRANTED_LOGISTICS_ADMIN,
                processor: RemoveProcessor::class
            ),
            new Patch(
                inputFormats: ['json' => ['application/merge-patch+json']],
                openapiContext: ['description' => 'Modifie un incoterms', 'summary' => 'Modifie un incoterms'],
                security: Role::GRANTED_LOGISTICS_WRITER,
                processor: PersistProcessor::class
            )
        ],
        inputFormats: 'json',
        outputFormats: 'jsonld',
        normalizationContext: [
            'groups' => ['id', 'incoterms-read'],
            'skip_null_values' => false,
            'openapi_definition_name' => 'incoterms-read'
        ],
        denormalizationContext: ['groups' => ['incoterms-write']],
        order: ['code' => 'asc']
    ),
    ORM\Entity,
    UniqueEntity(fields: ['code', 'deleted'], ignoreNull: false),
    UniqueEntity(fields: ['name', 'deleted'], ignoreNull: false)
]
class Incoterms extends Entity {
    #[
        ApiProperty(description: 'Code', example: 'DDP'),
        Assert\Length(min: 3, max: 25),
        Assert\NotBlank,
        ORM\Column(length: 25),
        Serializer\Groups(['incoterms-read', 'incoterms-write'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Nom', example: 'Delivered Duty Paid'),
        Assert\Length(min: 3, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50),
        Serializer\Groups(['incoterms-read', 'incoterms-write'])
    ]
    private ?string $name = null;

    public function getCode(): ?string {
        return $this->code;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
