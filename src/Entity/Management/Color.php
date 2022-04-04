<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'rgb' => 'partial']),
    ApiResource(
        description: 'Couleur',
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
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:color'],
            'openapi_definition_name' => 'Color-write'
        ],
        normalizationContext: [
            'groups' => ['read:color', 'read:id'],
            'openapi_definition_name' => 'Color-read'
        ],
    ),
    ORM\Entity,
    UniqueEntity('name'),
    UniqueEntity('rgb')
]
class Color extends Entity {
    #[
        ApiProperty(description: 'nom', required: true, example: 'Gris'),
        Assert\Length(min: 3, max: 20),
        Assert\NotBlank,
        ORM\Column(length: 20),
        Serializer\Groups(['read:color', 'write:color'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'rgb', example: '#848484'),
        Assert\Length(exactly: 7),
        Assert\NotBlank,
        ORM\Column(type: 'char', length: 7, options: ['charset' => 'ascii']),
        Serializer\Groups(['read:color', 'write:color'])
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
