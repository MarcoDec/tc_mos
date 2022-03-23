<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\ApiPlatform\Core\Annotation\ApiProperty;
use App\ApiPlatform\Core\Annotation\ApiSerializerGroups;
use App\Entity\Entity;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Couleur.
 */
#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'rgb' => 'partial']),
    ApiResource(
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les couleurs',
                    'summary' => 'Récupère les couleurs',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une couleur',
                    'summary' => 'Créer une couleur',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une couleur',
                    'summary' => 'Supprime une couleur',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une couleur',
                    'summary' => 'Modifie une couleur',
                ]
            ]
        ],
        denormalizationContext: [
            'groups' => ['Color-write'],
            'openapi_definition_name' => 'Color-write'
        ],
        normalizationContext: [
            'groups' => ['Color-read'],
            'openapi_definition_name' => 'Color-read'
        ]
    ),
    ApiSerializerGroups(inheritedRead: ['Color-read' => ['Color-write', 'Entity']], write: ['Color-write']),
    ORM\Entity,
    UniqueEntity('name'),
    UniqueEntity('rgb')
]
class Color extends Entity {
    /**
     * @var null|string Nom
     */
    #[
        ApiProperty(example: 'Gris', format: 'name'),
        Assert\Length(min: 3, max: 20),
        Assert\NotBlank,
        ORM\Column(length: 20),
        Serializer\Groups(groups: ['Color-read', 'Color-write'])
    ]
    private ?string $name = null;

    /**
     * @var null|string RGB
     */
    #[
        ApiProperty(example: '#848484', format: 'color'),
        Assert\CssColor(
            formats: Assert\CssColor::HEX_LONG,
            message: 'Cette valeur n\'est pas une couleur hexadécimale valide.'
        ),
        Assert\Length(exactly: 7),
        Assert\NotBlank,
        ORM\Column(type: 'char', length: 7, options: ['charset' => 'ascii']),
        Serializer\Groups(groups: ['Color-read', 'Color-write'])
    ]
    private ?string $rgb = null;

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getRgb(): ?string {
        return $this->rgb;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setRgb(?string $rgb): self {
        $this->rgb = $rgb;
        return $this;
    }
}
