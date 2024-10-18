<?php

namespace App\Entity\Mos;

use App\Entity\Entity;
use App\Doctrine\DBAL\Types\VoieType;
use App\Repository\Mos\VoieRepository;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['id', 'type', 'couleur1', 'couleur2', 'couleur3', 'marquage', 'reference', 'commentaire']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['connecteur']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['id' => 'exact', 'type' => 'exact', 'couleur1' => 'partial', 'couleur2' => 'partial', 'couleur3' => 'partial', 'marquage' => 'partial', 'reference' => 'partial', 'commentaire' => 'partial']),
    ApiResource(
        description: 'Voie',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:id', 'read:voie'],
                    'openapi_definition_name' => 'Voie-read',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les voies',
                    'summary' => 'Récupère les voies'
                ]
            ],

            'post' => [
                'denormalization_context' => [
                    'groups' => ['create:voie'],
                    'openapi_definition_name' => 'Voie-create'
                ],
                'openapi_context' => [
                    'description' => 'Créer une voie',
                    'summary' => 'Créer une voie'
                ]
            ]
        ],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une voie',
                    'summary' => 'Récupère une voie'
                ]
            ],

            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une voie',
                    'summary' => 'Supprime une voie'
                ]
            ],

            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une voie',
                    'summary' => 'Modifie une voie'
                ]
            ]
        ],
        denormalizationContext: [
            'groups' => ['write:voie'],
            'openapi_definition_name' => 'Voie-write'
        ],
        normalizationContext: [
            'groups' => ['read:voie'],
            'openapi_definition_name' => 'Voie-read',
            'skip_null_values' => false
        ],
        paginationClientEnabled: true
    ),
    ORM\Entity(repositoryClass: VoieRepository::class)
    ]
class Voie extends Entity {
    
    #[
        ApiProperty(description: 'Commentaire'),
        ORM\Column(length: 255, nullable: true),
        Serializer\Groups(['create:voie', 'read:voie', 'write:voie'])
    ]
    private ?string $commentaire = null;

    #[
        ApiProperty(description: 'Couleur principale du fil'),
        ORM\Column(length: 255, nullable: true),
        Serializer\Groups(['create:voie', 'read:voie', 'write:voie'])
    ]
    private ?string $couleur1 = null;

    #[ApiProperty(description: 'Couleur secondaire du fil'),
    ORM\Column(length: 255, nullable: true),
    Serializer\Groups(['create:voie', 'read:voie', 'write:voie'])
    ]
    private ?string $couleur2 = null;

    #[
        ApiProperty(description: 'Couleur tertiaire du fil'),
        ORM\Column(length: 255, nullable: true),
        Serializer\Groups(['create:voie', 'read:voie', 'write:voie'])
    ]
    private ?string $couleur3 = null;

    #[
        ApiProperty(description: 'Marquage du fil'),
        ORM\Column(length: 255, nullable: true),
        Serializer\Groups(['create:voie', 'read:voie', 'write:voie'])
    ]
    private ?string $marquage = null;

    #[
        ApiProperty(description: 'Référence fils/bouchon'),
        ORM\Column(length: 255, nullable: true),
        Serializer\Groups(['create:voie', 'read:voie', 'write:voie'])
    ]
    private ?string $reference = null;

    #[
        ApiProperty(description: 'Type', example: VoieType::TYPE_FIL, openapiContext: ['enum' => VoieType::TYPES]),
        Assert\Choice(choices: VoieType::TYPES),
        ORM\Column(type: 'voie_type', options: ['default' => VoieType::TYPE_VIDE]),
        Serializer\Groups(['create:voie', 'read:voie', 'write:voie'])
    ]
    private ?string $type = null;

    #[
        ApiProperty(description: 'Numéro de la voie'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(nullable: false),
        Serializer\Groups(['create:voie', 'read:voie', 'write:voie'])
    ]
    private ?int $num = null;

    #[
        ApiProperty(description: 'Connecteur', required: true),
        ORM\ManyToOne(inversedBy: 'voies'),
        Serializer\Groups(['create:voie', 'read:voie', 'write:voie'])
    ]
    private ?Connecteur $connecteur = null;

    public function __construct() {
        $this->num = 0;
        $this->connecteur = new Connecteur();
    }

    public function getCommentaire(): ?string {
        return $this->commentaire;
    }

    public function getCouleur1(): ?string {
        return $this->couleur1;
    }

    public function getCouleur2(): ?string {
        return $this->couleur2;
    }

    public function getCouleur3(): ?string {
        return $this->couleur3;
    }

    public function getMarquage(): ?string {
        return $this->marquage;
    }

    public function getReference(): ?string {
        return $this->reference;
    }

    public function getConnecteur(): ?Connecteur {
        return $this->connecteur;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function getNum(): int {
        return $this->num;
    }

    public function setCommentaire(?string $commentaire): static {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function setCouleur1(?string $couleur1): static {
        $this->couleur1 = $couleur1;

        return $this;
    }

    public function setCouleur2(?string $couleur2): static {
        $this->couleur2 = $couleur2;

        return $this;
    }

    public function setCouleur3(?string $couleur3): static {
        $this->couleur3 = $couleur3;

        return $this;
    }

    public function setMarquage(?string $marquage): static {
        $this->marquage = $marquage;

        return $this;
    }

    public function setReference(?string $reference): static {
        $this->reference = $reference;

        return $this;
    }

    public function setType(?string $type): static {
        $this->type = $type;

        return $this;
    }

    public function setConnecteur(?Connecteur $connecteur): static
    {
        $this->connecteur = $connecteur;

        return $this;
    }

    public function setNum(int $num): static {
        $this->num = $num;
        
        return $this;
    }
}
