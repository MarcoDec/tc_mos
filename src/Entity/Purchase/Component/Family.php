<?php

namespace App\Entity\Purchase\Component;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Family as AbstractFamily;
use App\Entity\Project\Operation\Type;
use App\Entity\Quality\Reception\ComponentReference;
use App\Entity\Traits\CodeTrait;
use App\Filter\OldRelationFilter;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['copperable']),
    ApiFilter(filterClass: OldRelationFilter::class, properties: ['parent']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['customsCode' => 'partial', 'name' => 'partial', 'code' => 'partial']),
    ApiResource(
        description: 'Famille de composant',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les familles de composant',
                    'summary' => 'Récupère les familles de composant',
                ]
            ],
            'post' => [
                'controller' => PlaceholderAction::class,
                'input_formats' => ['multipart'],
                'openapi_context' => [
                    'description' => 'Créer une famille de composant',
                    'summary' => 'Créer une famille de composant',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une famille de composant',
                    'summary' => 'Supprime une famille de composant',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'post' => [
                'controller' => PlaceholderAction::class,
                'input_formats' => ['multipart'],
                'method' => 'POST',
                'openapi_context' => [
                    'description' => 'Modifie une famille de composant',
                    'summary' => 'Modifie une famille de composant',
                ],
                'path' => '/component-families/{id}',
                'status' => 200
            ]
        ],
        shortName: 'ComponentFamily',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:code', 'write:customs-code', 'write:family', 'write:file', 'write:name', 'write:attribute'],
            'openapi_definition_name' => 'ComponentFamily-write'
        ],
        normalizationContext: [
            'groups' => ['read:code', 'read:customs-code', 'read:family', 'read:file', 'read:id', 'read:name', 'read:attribute'],
            'openapi_definition_name' => 'ComponentFamily-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity,
    ORM\Table(name: 'component_family'),
    UniqueEntity(['name', 'parent'])
]
class Family extends AbstractFamily {
    use CodeTrait;

    /** @var Collection<int, self> */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['remove'])]
    protected Collection $children;

    #[
        ApiProperty(description: 'Code', example: 'CAB'),
        Assert\Length(max: 3),
        ORM\Column(length: 3),
        Serializer\Groups(['read:code', 'write:code'])
    ]
    protected ?string $code = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Câbles'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name;

    /**
     * @var null|self
     */
    #[
        ApiProperty(description: 'Famille parente', readableLink: false, example: '/api/component-families/2'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    protected $parent;

    /**
     * @var Collection<int, Attribute>
     */
    #[
        ApiProperty(description: 'Attributs', readableLink: false, example: ['/api/attributes/2', '/api/attributes/18']),
        ORM\ManyToMany(fetch: 'EXTRA_LAZY', targetEntity: Attribute::class, mappedBy: 'families'),
        Serializer\Groups(['read:attributes', 'write:attributes'])
    ]
    private Collection $attributes;

    #[
        ApiProperty(description: 'Cuivré ', example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    private bool $copperable = false;

    /**
     * @var Collection<int, ComponentReference>
     */
    #[
        ApiProperty(description: 'References'),
        ORM\ManyToMany(targetEntity: ComponentReference::class, mappedBy: 'families'),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    private Collection $references;

    /**
     * @var Collection<int, Type>
     */
    #[
        ApiProperty(description: 'Type d\'opération', readableLink: false, example: ['/api/operation-types/5', '/api/operation-types/6']),
        ORM\ManyToMany(fetch: 'EXTRA_LAZY', targetEntity: Type::class, mappedBy: 'families'),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    private Collection $types;

    public function __construct() {
        parent::__construct();
        $this->attributes = new ArrayCollection();
        $this->references = new ArrayCollection();
        $this->types = new ArrayCollection();
    }

    final public function addAttribute(Attribute $attribute): self {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes[] = $attribute;
            $attribute->addFamily($this);
        }

        return $this;
    }

    final public function addReference(ComponentReference $references): self {
        if (!$this->references->contains($references)) {
            $this->references[] = $references;
            $references->addFamily($this);
        }

        return $this;
    }

    final public function addType(Type $type): self {
        if (!$this->types->contains($type)) {
            $this->types->add($type);
            $type->addFamily($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Attribute>
     */
    final public function getAttributes(): Collection {
        return $this->attributes;
    }

    final public function getCopperable(): ?bool {
        return $this->copperable;
    }

    #[
        ApiProperty(description: 'Icône', example: '/uploads/component-families/1.jpg'),
        Serializer\Groups(['read:file'])
    ]
    final public function getFilepath(): ?string {
        return parent::getFilepath();
    }

    /**
     * @return Collection<int, ComponentReference>
     */
    final public function getReferences(): Collection {
        return $this->references;
    }

    /**
     * @return Collection<int, Type>
     */
    final public function getTypes(): Collection {
        return $this->types;
    }

    final public function removeAttribute(Attribute $attribute): self {
        if ($this->attributes->removeElement($attribute)) {
            $attribute->removeFamily($this);
        }

        return $this;
    }

    final public function removeReference(ComponentReference $references): self {
        if ($this->references->removeElement($references)) {
            $references->removeFamily($this);
        }

        return $this;
    }

    final public function removeType(Type $type): self {
        if ($this->types->contains($type)) {
            $this->types->removeElement($type);
            $type->removeFamily($this);
        }
        return $this;
    }

    final public function setCopperable(bool $copperable): self {
        $this->copperable = $copperable;
        return $this;
    }
}
