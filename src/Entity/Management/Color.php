<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'ral' => 'partial', 'rgb' => 'partial']),
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
            'groups' => ['write:color', 'write:name'],
            'openapi_definition_name' => 'Color-write'
        ],
        normalizationContext: [
            'groups' => ['read:color', 'read:id', 'read:name'],
            'openapi_definition_name' => 'Color-read'
        ],
    ),
    ORM\Entity
]
class Color extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Gris'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'RAL', example: '17122018'),
        ORM\Column(length: 8, nullable: true),
        Serializer\Groups(['read:color', 'write:color'])
    ]
    private ?string $ral = null;

    #[
        ApiProperty(description: 'RGB', example: '#848484'),
        ORM\Column(length: 7, nullable: true),
        Serializer\Groups(['read:color', 'write:color'])
    ]
    private ?string $rgb = null;

    final public function getRal(): ?string {
        return $this->ral;
    }

    final public function getRgb(): ?string {
        return $this->rgb;
    }

    final public function setRal(?string $ral): self {
        $this->ral = $ral;
        return $this;
    }

    final public function setRgb(?string $rgb): self {
        $this->rgb = $rgb;
        return $this;
    }
}
