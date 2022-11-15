<?php

declare(strict_types=1);

namespace App\Entity\Purchase\Component;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\Type\Hr\Employee\Role;
use App\Entity\Entity;
use App\Repository\Purchase\Component\FamilyRepository;
use App\State\FamilyPersistProcessor;
use App\State\PersistProcessor;
use App\State\Purchase\Component\FamilyProvider;
use App\State\RemoveProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        shortName: 'ComponentFamily',
        description: 'Familles de composant',
        operations: [
            new GetCollection(
                uriTemplate: '/component-families/options',
                openapiContext: [
                    'description' => 'Récupère les familles pour les select',
                    'summary' => 'Récupère les familles pour les select'
                ],
                normalizationContext: [
                    'groups' => ['id', 'component-family-option'],
                    'skip_null_values' => false,
                    'openapi_definition_name' => 'component-family-option'
                ]
            ),
            new GetCollection(
                openapiContext: ['description' => 'Récupère les familles', 'summary' => 'Récupère les familles']
            ),
            new Post(
                openapiContext: ['description' => 'Créer une famille', 'summary' => 'Créer une famille'],
                processor: FamilyPersistProcessor::class
            ),
            new Post(
                uriTemplate: '/component-families/{id}',
                status: JsonResponse::HTTP_OK,
                openapiContext: ['description' => 'Modifie une famille', 'summary' => 'Modifie une famille'],
                processor: FamilyPersistProcessor::class
            ),
            new Delete(
                openapiContext: ['description' => 'Supprime une famille', 'summary' => 'Supprime une famille'],
                validationContext: ['groups' => ['delete']],
                processor: RemoveProcessor::class
            ),
            new Patch(
                inputFormats: ['json' => ['application/merge-patch+json']],
                openapiContext: ['description' => 'Modifie les attributs', 'summary' => 'Modifie les attributs'],
                normalizationContext: [
                    'groups' => ['id', 'component-family-attribute'],
                    'skip_null_values' => false,
                    'openapi_definition_name' => 'component-family-read-attribute'
                ],
                denormalizationContext: [
                    'groups' => ['component-family-attribute'],
                    'openapi_definition_name' => 'component-family-write-attribute'
                ],
                provider: FamilyProvider::class,
                processor: PersistProcessor::class
            )
        ],
        inputFormats: 'multipart',
        outputFormats: 'jsonld',
        normalizationContext: [
            'groups' => ['id', 'component-family-read'],
            'skip_null_values' => false,
            'openapi_definition_name' => 'component-family-read'
        ],
        denormalizationContext: ['groups' => ['component-family-write']],
        order: ['name' => 'asc'],
        paginationEnabled: false,
        security: Role::GRANTED_PROJECT_ADMIN
    ),
    Gedmo\Tree(type: 'nested'),
    ORM\Entity(repositoryClass: FamilyRepository::class),
    ORM\Table(name: 'component_family'),
    UniqueEntity(fields: ['name', 'deleted', 'parent'], ignoreNull: false)
]
class Family extends Entity {
    /** @var Collection<int, Attribute> */
    #[
        ApiProperty(
            description: 'Attributs',
            readableLink: false,
            writableLink: false,
            required: true,
            example: ['/api/attributes/1', '/api/attributes/2']
        ),
        ORM\ManyToMany(targetEntity: Attribute::class, mappedBy: 'families'),
        Serializer\Groups('component-family-attribute')
    ]
    private Collection $attributes;

    #[Assert\Length(exactly: 3), Assert\NotBlank, ORM\Column(type: 'char', length: 3)]
    private ?string $code = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $copperable = false;

    #[
        Assert\Length(min: 4, max: 10),
        ORM\Column(length: 10, nullable: true),
        Serializer\Groups(['component-family-read', 'component-family-write'])
    ]
    private ?string $customsCode = null;

    #[Assert\Image, Serializer\Groups('component-family-write')]
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

    public function __construct() {
        $this->attributes = new ArrayCollection();
    }

    public function addAttribute(Attribute $attribute): self {
        if ($this->attributes->contains($attribute) === false) {
            $this->attributes->add($attribute);
            $attribute->addFamily($this);
        }
        return $this;
    }

    public function generateFullIconName(): string {
        return $this->generateIconName().($this->file?->getExtension() ?: $this->file?->guessExtension());
    }

    public function generateIconName(): string {
        return "{$this->getId()}.";
    }

    /** @return Collection<int, Attribute> */
    public function getAttributes(): Collection {
        return $this->attributes;
    }

    #[ApiProperty(description: 'Code', required: true, example: 'CAB'), Serializer\Groups('component-family-read')]
    public function getCode(): ?string {
        return $this->code;
    }

    #[
        ApiProperty(description: 'Code douanier', required: true, example: '8544300089'),
        Serializer\Groups('component-family-read')
    ]
    public function getCustomsCode(): ?string {
        return $this->customsCode;
    }

    public function getDir(): string {
        return 'component-families';
    }

    public function getFile(): ?File {
        return $this->file;
    }

    #[ApiProperty(description: 'Nom complet', required: true, example: 'Câbles'), Serializer\Groups('component-family-read')]
    public function getFullName(): ?string {
        return empty($this->parent) ? $this->name : "{$this->parent->getFullName()}/$this->name";
    }

    #[Serializer\Groups('component-family-read')]
    public function getIcon(): ?string {
        return empty($this->file) ? null : explode('/public', $this->file->getPathname())[1];
    }

    public function getLft(): ?int {
        return $this->lft;
    }

    public function getLvl(): ?int {
        return $this->lvl;
    }

    #[ApiProperty(description: 'Nom', required: true, example: 'Câbles'), Serializer\Groups('component-family-read')]
    public function getName(): ?string {
        return $this->name;
    }

    #[
        ApiProperty(description: 'Parent', readableLink: false, writableLink: false, required: true, example: '/api/component-families/1'),
        Serializer\Groups('component-family-read')
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

    #[ApiProperty(required: true), Serializer\Groups('component-family-option')]
    public function getText(): ?string {
        return $this->getFullName();
    }

    #[Assert\IsFalse(message: 'This family has children.', groups: ['delete'])]
    public function hasChildren(): bool {
        return $this->rgt - $this->lft > 1;
    }

    #[
        ApiProperty(description: 'Cuivré', required: true, example: true),
        Serializer\Groups('component-family-read')
    ]
    public function isCopperable(): bool {
        return $this->copperable;
    }

    public function removeAttribute(Attribute $attribute): self {
        if ($this->attributes->contains($attribute)) {
            $this->attributes->removeElement($attribute);
            $attribute->removeFamily($this);
        }
        return $this;
    }

    #[ApiProperty(description: 'Code', required: false, example: 'CAB'), Serializer\Groups('component-family-write')]
    public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    #[
        ApiProperty(description: 'Cuivré', required: false, example: true),
        Serializer\Groups('component-family-write')
    ]
    public function setCopperable(bool $copperable): self {
        $this->copperable = $copperable;
        return $this;
    }

    #[
        ApiProperty(description: 'Code douanier', required: false, example: '8544300089'),
        Serializer\Groups('component-family-write')
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

    #[ApiProperty(description: 'Nom', required: false, example: 'Câbles'), Serializer\Groups('component-family-write')]
    public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    #[
        ApiProperty(description: 'Parent', readableLink: false, writableLink: false, required: false, example: '/api/component-families/1'),
        Serializer\Groups('component-family-write')
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
