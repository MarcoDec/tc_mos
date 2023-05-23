<?php

declare(strict_types=1);

namespace App\Entity\Management;

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
    ApiFilter(filterClass: OrderFilter::class, properties: ['name', 'rbg']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'rbg' => 'partial']),
    ApiResource(
        description: 'Couleur',
        operations: [
            new GetCollection(
                openapiContext: ['description' => 'Récupère les couleurs', 'summary' => 'Récupère les couleurs']
            ),
            new Post(
                openapiContext: ['description' => 'Créer une couleur', 'summary' => 'Créer une couleur'],
                processor: PersistProcessor::class
            ),
            new Delete(
                openapiContext: ['description' => 'Supprime une couleur', 'summary' => 'Supprime une couleur'],
                processor: RemoveProcessor::class
            ),
            new Patch(
                inputFormats: ['json' => ['application/merge-patch+json']],
                openapiContext: ['description' => 'Modifie une couleur', 'summary' => 'Modifie une couleur'],
                processor: PersistProcessor::class
            )
        ],
        inputFormats: 'json',
        outputFormats: 'jsonld',
        normalizationContext: [
            'groups' => ['id', 'color-read'],
            'skip_null_values' => false,
            'openapi_definition_name' => 'color-read'
        ],
        denormalizationContext: ['groups' => ['color-write']],
        order: ['name' => 'asc'],
        security: Role::GRANTED_MANAGEMENT_ADMIN
    ),
    ORM\Entity,
    UniqueEntity(fields: ['name', 'deleted'], ignoreNull: false),
    UniqueEntity(fields: ['rgb', 'deleted'], ignoreNull: false)
]
class Color extends Entity {
    #[
        ApiProperty(description: 'nom', example: 'Gris'),
        Assert\Length(min: 3, max: 20),
        Assert\NotBlank,
        ORM\Column(length: 20),
        Serializer\Groups(['color-read', 'color-write'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'rgb', example: '#848484'),
        Assert\CssColor(formats: Assert\CssColor::HEX_LONG),
        Assert\NotBlank,
        ORM\Column(type: 'char', length: 7),
        Serializer\Groups(['color-read', 'color-write'])
    ]
    private ?string $rgb = null;

    public function getName(): ?string {
        return $this->name;
    }

    public function getRgb(): ?string {
        return $this->rgb;
    }

    public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    public function setRgb(?string $rgb): self {
        $this->rgb = $rgb;
        return $this;
    }
}
