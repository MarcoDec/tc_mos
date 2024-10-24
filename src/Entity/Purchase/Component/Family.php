<?php

namespace App\Entity\Purchase\Component;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Collection;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Family as AbstractFamily;
use App\Entity\Quality\Reception\Check;
use App\Entity\Quality\Reception\Reference\Purchase\FamilyReference;
use App\Filter\RelationFilter;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['copperable']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['parent']),
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
            'options' => [
                'controller' => PlaceholderAction::class,
                'filters' => [],
                'method' => 'GET',
                'normalization_context' => [
                    'groups' => ['read:id', 'read:family:option'],
                    'openapi_definition_name' => 'ComponentFamily-options',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les familles pour les select',
                    'summary' => 'Récupère les familles pour les select',
                ],
                'order' => ['name' => 'asc'],
                'pagination_enabled' => false,
                'path' => '/component-families/options'
            ],
            'post' => [
                'controller' => PlaceholderAction::class,
                'input_formats' => ['multipart'],
                'openapi_context' => [
                    'description' => 'Créer une famille de composant',
                    'summary' => 'Créer une famille de composant',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une famille de composant',
                    'summary' => 'Supprime une famille de composant',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'denormalization_context' => [
                    'groups' => ['patch:family'],
                    'openapi_definition_name' => 'ComponentFamily-patch-write'
                ],
                'normalization_context' => [
                    'groups' => ['patch:family'],
                    'openapi_definition_name' => 'ComponentFamily-patch-read'
                ],
                'openapi_context' => [
                    'description' => 'Modifie les attributs',
                    'summary' => 'Modifie les attributs',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ],
            'post' => [
                'controller' => PlaceholderAction::class,
                'input_formats' => ['multipart'],
                'method' => 'POST',
                'openapi_context' => [
                    'description' => 'Modifie une famille de composant',
                    'summary' => 'Modifie une famille de composant',
                ],
                'path' => '/component-families/{id}',
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')',
                'status' => 200
            ]
        ],
        shortName: 'ComponentFamily',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:family', 'write:component-family', 'write:file'],
            'openapi_definition_name' => 'ComponentFamily-write'
        ],
        normalizationContext: [
            'groups' => ['read:family', 'read:component-family', 'read:file', 'read:id'],
            'openapi_definition_name' => 'ComponentFamily-read',
            'skip_null_values' => false
        ],
        paginationEnabled: false
    ),
    ORM\Entity,
    ORM\Table(name: 'component_family'),
    UniqueEntity(['name', 'parent'])
]
class Family extends AbstractFamily {
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['remove'])]
    protected DoctrineCollection $children;

    #[
        ApiProperty(description: 'Lien image'),
        ORM\Column(type: 'string'),
        Serializer\Groups(['read:file', 'read:component-family'])
    ]
    protected ?string $filePath = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Câbles'),
        Assert\Length(min: 3, max: 40),
        Assert\NotBlank,
        ORM\Column(length: 40),
        Serializer\Groups(['read:family', 'write:family', 'write:component-family', 'read:component-family'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Famille parente', readableLink: false, example: '/api/component-families/2'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:family', 'write:family', 'write:component-family', 'read:component-family'])
    ]
    protected $parent;

    /** @var DoctrineCollection<int, Attribute> */
    #[
        ApiProperty(description: 'Attributs', readableLink: false, required: true, example: ['/api/attributes/1', '/api/attributes/2']),
        ORM\ManyToMany(targetEntity: Attribute::class, mappedBy: 'families'),
        Serializer\Groups(['patch:family'])
    ]
    private DoctrineCollection $attributes;

    #[
        ApiProperty(description: 'Code ', required: true, example: 'CAB'),
        Assert\Length(exactly: 3),
        Assert\NotBlank,
        ORM\Column(type: 'char', length: 3),
        Serializer\Groups(['read:family', 'write:family', 'read:component-family', 'write:component-family'])
    ]
    private ?string $code = null;

    /** @var DoctrineCollection<int, Component> */
    #[ORM\OneToMany(mappedBy: 'family', targetEntity: Component::class, fetch: 'EXTRA_LAZY')]
    private DoctrineCollection $components;

    #[
        ApiProperty(description: 'Cuivré ', example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:family', 'write:family', 'write:component-family', 'read:component-family'])
    ]
    private bool $copperable = false;

    public function __construct() {
        parent::__construct();
        $this->attributes = new ArrayCollection();
        $this->components = new ArrayCollection();
    }

    final public function addAttribute(Attribute $attribute): self {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes->add($attribute);
            $attribute->addFamily($this);
            foreach ($this->children as $child) {
                $child->addAttribute($attribute);
            }
        }
        return $this;
    }

    final public function addComponent(Component $component): self {
        if (!$this->components->contains($component)) {
            $this->components->add($component);
            $component->setFamily($this);
        }
        return $this;
    }

    /**
     * @return DoctrineCollection<int, Attribute>
     */
    final public function getAttributes(): DoctrineCollection {
        return $this->attributes;
    }

    final public function getCode(): ?string {
        if ($this->code === null && $this->parent !== null) {
            return $this->parent->getCode();
        }
        return $this->code;
    }
    public function getCustomsCode(): ?string {
        if ($this->customsCode === null && $this->parent !== null) {
            return $this->parent->getCustomsCode();
        }
        return $this->customsCode;
    }
    /**
     * @return DoctrineCollection<int, Component>
     */
    final public function getComponents(): DoctrineCollection {
        return $this->components;
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

    #[Serializer\Groups(['read:family:option'])]
    final public function getText(): ?string {
        return $this->getFullName();
    }

    final public function hasComponents(): bool {
        if (!$this->components->isEmpty()) {
            return true;
        }
        foreach ($this->children as $child) {
            if ($child->hasComponents()) {
                return true;
            }
        }
        return false;
    }

    final public function removeAttribute(Attribute $attribute): self {
        if ($this->attributes->contains($attribute)) {
            $this->attributes->removeElement($attribute);
            $attribute->removeFamily($this);
            foreach ($this->children as $child) {
                $child->removeAttribute($attribute);
            }
        }
        return $this;
    }

    final public function removeComponent(Component $component): self {
        if ($this->components->contains($component)) {
            $this->components->removeElement($component);
            if ($component->getFamily() === $this) {
                $component->setFamily(null);
            }
        }
        return $this;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setCopperable(bool $copperable): self {
        $this->copperable = $copperable;
        return $this;
    }
}
