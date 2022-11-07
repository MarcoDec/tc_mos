<?php

declare(strict_types=1);

namespace App\Entity\Purchase\Component;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\Type\Purchase\Component\EnumAttributeType;
use App\Entity\Entity;
use App\Filter\SearchFilter;
use App\State\PersistProcessor;
use App\State\RemoveProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['description', 'name', 'type']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['description' => 'partial', 'name' => 'partial', 'type' => 'exact']),
    ApiResource(
        description: 'Attribut',
        operations: [
            new GetCollection(
                openapiContext: ['description' => 'Récupère les attributs', 'summary' => 'Récupère les attributs']
            ),
            new Post(
                openapiContext: ['description' => 'Créer un attribut', 'summary' => 'Créer un attribut'],
                processor: PersistProcessor::class
            ),
            new Delete(
                openapiContext: ['description' => 'Supprime un attribut', 'summary' => 'Supprime un attribut'],
                processor: RemoveProcessor::class
            ),
            new Patch(
                inputFormats: ['json' => ['application/merge-patch+json']],
                openapiContext: ['description' => 'Modifie un attribut', 'summary' => 'Modifie un attribut'],
                processor: PersistProcessor::class
            )
        ],
        inputFormats: 'json',
        outputFormats: 'jsonld',
        normalizationContext: ['groups' => ['attribute-read'], 'skip_null_values' => false],
        denormalizationContext: ['groups' => ['attribute-write']],
        order: ['name' => 'asc']
    ),
    ORM\Entity,
    UniqueEntity(fields: ['name', 'deleted'], ignoreNull: false)
]
class Attribute extends Entity {
    #[Assert\Length(min: 3, max: 255), ORM\Column(nullable: true)]
    private ?string $description = null;

    #[
        ApiProperty(description: 'Nom', example: 'Longueur'),
        Assert\Length(min: 3, max: 255),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['attribute-read', 'attribute-write'])
    ]
    private ?string $name = null;

    #[Assert\NotBlank, ORM\Column(type: 'attribute')]
    private EnumAttributeType $type = EnumAttributeType::TYPE_TEXT;

    #[
        ApiProperty(description: 'Description', required: true, example: 'Longueur de l\'embout'),
        Serializer\Groups('attribute-read')
    ]
    public function getDescription(): ?string {
        return $this->description;
    }

    public function getName(): ?string {
        return $this->name;
    }

    #[
        ApiProperty(
            description: 'Type',
            required: true,
            default: EnumAttributeType::DEFAULT,
            example: EnumAttributeType::DEFAULT,
            openapiContext: ['enum' => EnumAttributeType::ENUM]
        ),
        Serializer\Groups('attribute-read')
    ]
    public function getType(): string {
        return $this->type->value;
    }

    #[
        ApiProperty(description: 'Description', required: false, example: 'Longueur de l\'embout'),
        Serializer\Groups('attribute-write')
    ]
    public function setDescription(?string $description): self {
        $this->description = $description === null ? $description : trim($description);
        return $this;
    }

    public function setName(?string $name): self {
        $this->name = $name === null ? $name : trim($name);
        return $this;
    }

    #[
        ApiProperty(
            description: 'Type',
            required: false,
            default: EnumAttributeType::DEFAULT,
            example: EnumAttributeType::DEFAULT,
            openapiContext: ['enum' => EnumAttributeType::ENUM]
        ),
        Serializer\Groups('attribute-write')
    ]
    public function setType(string $type): self {
        $this->type = EnumAttributeType::from(trim($type));
        return $this;
    }
}
