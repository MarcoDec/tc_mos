<?php

namespace App\Entity\Mos;

use App\Controller\Connecteur\ConnecteurGammeController;
use App\Entity\Entity;
use App\Repository\Mos\ConnecteurRepository;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['id', 'idProduct', 'idBom', 'idComponent', 'commentaire', 'difficulteAssemblage']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['voies', 'accessoires']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['id' => 'exact', 'idProduct' => 'exact', 'idBom' => 'exact', 'idComponent' => 'exact', 'commentaire' => 'partial', 'difficulteAssemblage' => 'exact']),
    ApiResource(
        description: 'Connecteur',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:id', 'read:connecteur'],
                    'openapi_definition_name' => 'Connecteur-read',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les connecteurs',
                    'summary' => 'Récupère les connecteurs'
                ]
            ],

            'post' => [
                'denormalization_context' => [
                    'groups' => ['create:connecteur'],
                    'openapi_definition_name' => 'Connecteur-create'
                ],
                'openapi_context' => [
                    'description' => 'Créer un connecteur',
                    'summary' => 'Créer un connecteur'
                ]
            ],

            'isExistGamme' => [
                'method' => 'POST',
                'path' => '/isExistGamme',
                'controller' => ConnecteurGammeController::class,
            ]
        ],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un connecteur',
                    'summary' => 'Récupère un connecteur'
                ]
            ],

            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un connecteur',
                    'summary' => 'Supprime un connecteur'
                ]
            ],

            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un connecteur',
                    'summary' => 'Modifie un connecteur'
                ]
            ]
        ],
        denormalizationContext: [
            'groups' => ['write:connecteur'],
            'openapi_definition_name' => 'Connecteur-write'
        ],
        normalizationContext: [
            'groups' => ['read:connecteur'],
            'openapi_definition_name' => 'Connecteur-read',
            'skip_null_values' => false
        ],
        paginationClientEnabled: true
    ),
    ORM\Entity(repositoryClass: ConnecteurRepository::class)
]
class Connecteur extends Entity {
    
    /** @var DoctrineCollection<int, Accessoire> */
    #[
        ORM\OneToMany(mappedBy: 'connecteur', targetEntity: Accessoire::class),
        Serializer\Groups(['read:connecteur'])
    ]
    private DoctrineCollection $accessoires;

    #[
        ApiProperty(description: 'Commentaire'),
        ORM\Column(length: 255, nullable: true),
        Serializer\Groups(['create:connecteur', 'read:connecteur', 'write:connecteur'])
    ]
    private ?string $commentaire = null;

    #[
        ApiProperty(description: 'Difficulté de l\assemblage (entre 1 et 5)'),
        ORM\Column(nullable: true),
        Assert\range(min: 0, max: 5, notInRangeMessage: "La valeur doit être comprise entre {{ min }} et {{ max }} ou null."),
        Serializer\Groups(['create:connecteur', 'read:connecteur', 'write:connecteur'])
    ]
    private ?int $difficulteAssemblage = null;

    #[
        ApiProperty(description: 'Id Bom'),
        ORM\Column(nullable: true),
        Serializer\Groups(['create:connecteur', 'read:connecteur', 'write:connecteur'])
    ]
    private ?int $idBom = null;


    #[
        ApiProperty(description: 'Id du composant'),
        ORM\Column(nullable: true),
        Serializer\Groups(['create:connecteur', 'read:connecteur', 'write:connecteur'])
    ]
    private ?int $idComponent = null;

    #[
        ApiProperty(description: 'Id du produit'),
        ORM\Column(nullable: true),
        Serializer\Groups(['create:connecteur', 'read:connecteur', 'write:connecteur'])
    ]
    private ?int $idProduct = null;

    #[
        ApiProperty(description: 'Lien image'),
        ORM\Column(length: 255, nullable: true),
        Serializer\Groups(['create:connecteur', 'read:connecteur', 'read:file', 'write:connecteur'])
    ]
    private ?string $image = null;

    #[
        ApiProperty(description: 'Connecteur validé'),
        ORM\Column(options: ["default" => false]),
        Serializer\Groups(['read:connecteur', 'write:connecteur'])
    ]
    private ?bool $valid = false;

    /** @var DoctrineCollection<int, Voie> */
    #[
        ORM\OneToMany(mappedBy: 'connecteur', targetEntity: Voie::class),
        Serializer\Groups(['read:connecteur'])
    ]
    private DoctrineCollection $voies;

    public function __construct() {
        $this->accessoires = new ArrayCollection();
        $this->voies = new ArrayCollection();
    }

    public function addAccessoire(Accessoire $accessoire): static {
        if (!$this->accessoires->contains($accessoire)) {
            $this->accessoires->add($accessoire);
            $accessoire->setConnecteur($this);
        }

        return $this;
    }

    public function addVoie(Voie $voie): static {
        if (!$this->voies->contains($voie)) {
            $this->voies->add($voie);
            $voie->setConnecteur($this);
        }

        return $this;
    }

    /**
     * @return DoctrineCollection<int, Accessoire>
     */
    public function getAccessoires(): DoctrineCollection {
        return $this->accessoires;
    }

    public function getCommentaire(): ?string {
        return $this->commentaire;
    }

    public function getDifficulteAssemblage(): ?int {
        return $this->difficulteAssemblage;
    }

    public function getIdBom(): ?int {
        return $this->idBom;
    }

    public function getIdComponent(): ?int {
        return $this->idComponent;
    }

    public function getIdProduct(): ?int {
        return $this->idProduct;
    }

    public function getImage(): ?string {
        return $this->image;
    }

    /**
     * @return DoctrineCollection<int, Voie>
     */
    public function getVoies(): DoctrineCollection {
        return $this->voies;
    }

    public function removeAccessoire(Accessoire $accessoire): static {
        if ($this->accessoires->removeElement($accessoire)) {
            // set the owning side to null (unless already changed)
            if ($accessoire->getConnecteur() === $this) {
                $accessoire->setConnecteur(null);
            }
        }

        return $this;
    }

    public function removeVoie(Voie $voie): static {
        if ($this->voies->removeElement($voie)) {
            // set the owning side to null (unless already changed)
            if ($voie->getConnecteur() === $this) {
                $voie->setConnecteur(null);
            }
        }

        return $this;
    }

    public function setCommentaire(?string $commentaire): static {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function setDifficulteAssemblage(?int $difficulteAssemblage): static {
        $this->difficulteAssemblage = $difficulteAssemblage;

        return $this;
    }

    public function setIdBom(?int $idBom): static {
        $this->idBom = $idBom;

        return $this;
    }

    public function setIdComponent(?int $idComponent): static {
        $this->idComponent = $idComponent;

        return $this;
    }

    public function setIdProduct(?int $idProduct): static {
        $this->idProduct = $idProduct;

        return $this;
    }

    public function setImage(?string $image): static {
        $this->image = $image;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }
}
