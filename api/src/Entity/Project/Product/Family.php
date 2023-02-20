<?php

declare(strict_types=1);

namespace App\Entity\Project\Product;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Doctrine\Type\Hr\Employee\Role;
use App\Entity\Entity;
use App\State\FamilyPersistProcessor;
use App\State\RemoveProcessor;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        shortName: 'ProductFamily',
        description: 'Familles de produit',
        operations: [
            new GetCollection(
                openapiContext: ['description' => 'Récupère les familles', 'summary' => 'Récupère les familles']
            ),
            new Post(
                openapiContext: ['description' => 'Créer une famille', 'summary' => 'Créer une famille'],
                processor: FamilyPersistProcessor::class
            ),
            new Post(
                uriTemplate: '/product-families/{id}',
                status: JsonResponse::HTTP_OK,
                openapiContext: ['description' => 'Modifie une famille', 'summary' => 'Modifie une famille'],
                processor: FamilyPersistProcessor::class
            ),
            new Delete(
                openapiContext: ['description' => 'Supprime une famille', 'summary' => 'Supprime une famille'],
                validationContext: ['groups' => ['delete']],
                processor: RemoveProcessor::class
            )
        ],
        inputFormats: 'multipart',
        outputFormats: 'jsonld',
        normalizationContext: [
            'groups' => ['id', 'product-family-read'],
            'skip_null_values' => false,
            'openapi_definition_name' => 'product-family-read'
        ],
        denormalizationContext: ['groups' => ['product-family-write']],
        order: ['name' => 'asc'],
        paginationEnabled: false,
        security: Role::GRANTED_PROJECT_ADMIN
    ),
    Gedmo\Tree(type: 'nested'),
    ORM\Entity(repositoryClass: NestedTreeRepository::class),
    ORM\Table(name: 'product_family'),
    UniqueEntity(fields: ['name', 'deleted', 'parent'], ignoreNull: false)
]
class Family extends Entity {
    #[
        Assert\Length(min: 4, max: 10),
        ORM\Column(length: 10, nullable: true),
        Serializer\Groups(['product-family-read', 'product-family-write'])
    ]
    private ?string $customsCode = null;

    #[Assert\Image,Serializer\Groups('product-family-write')]
    private ?File $file = null;

    #[Gedmo\TreeLeft, ORM\Column]
    private ?int $lft = null;

    #[Gedmo\TreeLevel, ORM\Column]
    private ?int $lvl = null;

    #[Assert\Length(min: 3, max: 30), Assert\NotBlank, ORM\Column(length: 30)]
    private ?string $name = null;

    #[Gedmo\TreeParent, ORM\ManyToOne(targetEntity: self::class)]
    private ?self $parent = null;

    #[Gedmo\TreeRight, ORM\Column]
    private ?int $rgt = null;

    #[Gedmo\TreeRoot, ORM\ManyToOne(targetEntity: self::class)]
    private ?self $root = null;

    public function generateFullIconName(): string {
        return $this->generateIconName().($this->file?->getExtension() ?: $this->file?->guessExtension());
    }

    public function generateIconName(): string {
        return "{$this->getId()}.";
    }

    #[
        ApiProperty(description: 'Code douanier', required: true, example: '8544300089'),
        Serializer\Groups('product-family-read')
    ]
    public function getCustomsCode(): ?string {
        return $this->customsCode;
    }

    public function getDir(): string {
        return 'product-families';
    }

    public function getFile(): ?File {
        return $this->file;
    }

    #[ApiProperty(description: 'Nom complet', required: true, example: 'Faisceaux'), Serializer\Groups('product-family-read')]
    public function getFullName(): ?string {
        return empty($this->parent) ? $this->name : "{$this->parent->getFullName()}/$this->name";
    }

    #[Serializer\Groups('product-family-read')]
    public function getIcon(): ?string {
        return empty($this->file) ? null : explode('/public', $this->file->getPathname())[1];
    }

    public function getLft(): ?int {
        return $this->lft;
    }

    public function getLvl(): ?int {
        return $this->lvl;
    }

    #[ApiProperty(description: 'Nom', required: true, example: 'Faisceaux'), Serializer\Groups('product-family-read')]
    public function getName(): ?string {
        return $this->name;
    }

    #[
        ApiProperty(description: 'Parent', readableLink: false, writableLink: false, required: true, example: '/api/product-families/1'),
        Serializer\Groups('product-family-read')
    ]
    public function getParent(): ?self {
        return $this->parent;
    }

    public function getRgt(): ?int {
        return $this->rgt;
    }

    public function getRoot(): ?self {
        return $this->root;
    }

    #[Assert\IsFalse(message: 'This family has children.', groups: ['delete'])]
    public function hasChildren(): bool {
        return $this->rgt - $this->lft > 1;
    }

    #[
        ApiProperty(description: 'Code douanier', required: false, example: '8544300089'),
        Serializer\Groups('product-family-write')
    ]
    public function setCustomsCode(?string $customsCode): self {
        $this->customsCode = $customsCode;
        return $this;
    }

    public function setFile(?File $file): self {
        $this->file = $file;
        return $this;
    }

    public function setLft(?int $lft): self {
        $this->lft = $lft;
        return $this;
    }

    public function setLvl(?int $lvl): self {
        $this->lvl = $lvl;
        return $this;
    }

    #[ApiProperty(description: 'Nom', required: false, example: 'Faisceaux'), Serializer\Groups('product-family-write')]
    public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    #[
        ApiProperty(description: 'Parent', readableLink: false, writableLink: false, required: false, example: '/api/product-families/1'),
        Serializer\Groups('product-family-write')
    ]
    public function setParent(?self $parent): self {
        $this->parent = $parent;
        return $this;
    }

    public function setRgt(?int $rgt): self {
        $this->rgt = $rgt;
        return $this;
    }

    public function setRoot(?self $root): self {
        $this->root = $root;
        return $this;
    }
}
