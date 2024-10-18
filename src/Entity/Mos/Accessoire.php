<?php

namespace App\Entity\Mos;

use App\Entity\Entity;
use App\Repository\Mos\AccessoireRepository;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['id', 'idComponent', 'commentaire']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['connecteur', 'targetedConnecteur']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['id' => 'exact', 'idComponent' => 'exact', 'commentaire' => 'partial']),
    ApiResource(
        description: 'Accessoire',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:id', 'read:accessoire'],
                    'openapi_definition_name' => 'Accessoire-read',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les accessoires',
                    'summary' => 'Récupère les accessoires'
                ]
            ],

            'post' => [
                'denormalization_context' => [
                    'groups' => ['create:accessoire'],
                    'openapi_definition_name' => 'Accessoire-create'
                ],
                'openapi_context' => [
                    'description' => 'Créer un accessoire',
                    'summary' => 'Créer un accessoire'
                ]
            ]
        ],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un accessoire',
                    'summary' => 'Récupère un accessoire'
                ]
            ],

            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un accessoire',
                    'summary' => 'Supprime un accessoire'
                ]
            ],

            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un accessoire',
                    'summary' => 'Modifie un accessoire'
                ]
            ]
        ],
        denormalizationContext: [
            'groups' => ['write:accessoire'],
            'openapi_definition_name' => 'Accessoire-write'
        ],
        normalizationContext: [
            'groups' => ['read:accessoire'],
            'openapi_definition_name' => 'Accessoire-read',
            'skip_null_values' => false
        ],
        paginationClientEnabled: true
    ),
    ORM\Entity(repositoryClass: AccessoireRepository::class)
]
class Accessoire extends Entity {
    
    #[
        ApiProperty(description: 'Commentaire', example: 'indication de montage'),
        ORM\Column(length: 255, nullable: true),
        Serializer\Groups(['create:accessoire', 'read:accessoire', 'write:accessoire'])
    ]
    private ?string $commentaire = null;

    #[
        ApiProperty(description: 'Id du composant associé', example: 'XXX'),
        ORM\Column(nullable: true),
        Serializer\Groups(['create:accessoire', 'read:accessoire', 'write:accessoire'])
    ]
    private ?int $idComponent = null;

    #[
        ApiProperty(description: 'Lien image'),
        ORM\Column(length: 255, nullable: true),
        Serializer\Groups(['create:accessoire', 'read:accessoire', 'read:file', 'write:accessoire'])
    ]
    private ?string $image = null;

    #[
        ApiProperty(description: 'Référence du connecteur parent', example: 'XXX'),
        ORM\ManyToOne(inversedBy: 'accessoires', targetEntity: Connecteur::class),
        Serializer\Groups(['create:accessoire', 'read:accessoire', 'read:accessoire:connecteur', 'write:accessoire'])
    ]
    private ?Connecteur $connecteur = null;

    #[
        ApiProperty(description: 'Référence du connecteur accessoire', example: 'XXX'),
        ORM\OneToOne(targetEntity: Connecteur::class),
        Serializer\Groups(['create:accessoire', 'read:accessoire', 'read:accessoire:connecteur', 'write:accessoire'])
    ]
    private ?Connecteur $targetedConnecteur = null;

    public function getCommentaire(): ?string {
        return $this->commentaire;
    }

    public function getIdComponent(): ?int {
        return $this->idComponent;
    }

    public function getImage(): ?string {
        return $this->image;
    }

    public function getConnecteur(): ?Connecteur {
        return $this->connecteur;
    }

    public function getTargetedConnecteur(): ?Connecteur {
        return $this->targetedConnecteur;
    }

    public function setCommentaire(?string $commentaire): static {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function setIdComponent(?int $idComponent): static {
        $this->idComponent = $idComponent;

        return $this;
    }

    public function setImage(?string $image): static {
        $this->image = $image;

        return $this;
    }

    public function setConnecteur(?Connecteur $connecteur): static {
        $this->connecteur = $connecteur;

        return $this;
    }

    public function setTargetedConnecteur(?Connecteur $targetedConnecteur): static {
        $this->targetedConnecteur = $targetedConnecteur;

        return $this;
    }
}
