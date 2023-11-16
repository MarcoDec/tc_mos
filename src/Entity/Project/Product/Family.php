<?php

namespace App\Entity\Project\Product;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Collection;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Family as AbstractFamily;
use App\Entity\Quality\Reception\Check;
use App\Entity\Quality\Reception\Reference\Selling\FamilyReference;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Famille de produit',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les familles de produit',
                    'summary' => 'Récupère les familles de produit',
                ]
            ],
            'options' => [
                'controller' => PlaceholderAction::class,
                'method' => 'GET',
                'normalization_context' => [
                    'groups' => ['read:id', 'read:product-family:option'],
                    'openapi_definition_name' => 'Product-family-options',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les familles de produit pour les select',
                    'summary' => 'Récupère les familles de produit  pour les select',
                ],
                'order' => ['name' => 'asc'],
                'path' => '/product-families/options'
            ],
            'post' => [
                'controller' => PlaceholderAction::class,
                'input_formats' => ['multipart'],
                'openapi_context' => [
                    'description' => 'Créer une famille de produit',
                    'summary' => 'Créer une famille de produit',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_ADMIN.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une famille de produit',
                    'summary' => 'Supprime une famille de produit',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'post' => [
                'controller' => PlaceholderAction::class,
                'input_formats' => ['multipart'],
                'method' => 'POST',
                'openapi_context' => [
                    'description' => 'Modifie une famille de produit',
                    'summary' => 'Modifie une famille de produit',
                ],
                'path' => '/product-families/{id}',
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_ADMIN.'\')',
                'status' => 200
            ]
        ],
        shortName: 'ProductFamily',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PROJECT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:family', 'write:file'],
            'openapi_definition_name' => 'ProductFamily-write'
        ],
        normalizationContext: [
            'groups' => ['read:product-family', 'read:file', 'read:id'],
            'openapi_definition_name' => 'ProductFamily-read',
            'skip_null_values' => false
        ],
        paginationEnabled: false
    ),
    ORM\Entity,
    ORM\Table(name: 'product_family'),
    UniqueEntity(['name', 'parent'])
]
class Family extends AbstractFamily {
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['remove'])]
    protected DoctrineCollection $children;

    #[
        ApiProperty(description: 'Lien image'),
        ORM\Column(type: 'string'),
        Serializer\Groups(['read:file', 'read:product-family'])
    ]
    protected ?string $filePath = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Faisceaux'),
        Assert\Length(min: 3, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50),
        Serializer\Groups(['read:product-family', 'write:family', 'read:product-family:option'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Famille parente', readableLink: false, example: '/api/product-families/1'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:product-family', 'write:family'])
    ]
    protected $parent;

    /** @var DoctrineCollection<int, FamilyReference> */
    #[ORM\ManyToMany(targetEntity: FamilyReference::class, mappedBy: 'items')]
    private DoctrineCollection $references;

    public function __construct() {
        parent::__construct();
        $this->references = new ArrayCollection();
    }

    final public function addReference(FamilyReference $reference): self {
        if (!$this->references->contains($reference)) {
            $this->references->add($reference);
            $reference->addItem($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Check<Product, self>>
     */
    final public function getChecks(): Collection {
        return Collection::collect($this->references->getValues())
            ->map(static function (FamilyReference $reference): Check {
                /** @var Check<Product, self> $check */
                $check = new Check();
                return $check->setReference($reference);
            });
    }

    final public function getFilepath(): ?string {
        return parent::getFilepath();
    }

    /**
     * @return DoctrineCollection<int, FamilyReference>
     */
    final public function getReferences(): DoctrineCollection {
        return $this->references;
    }

    final public function removeReference(FamilyReference $reference): self {
        if ($this->references->contains($reference)) {
            $this->references->removeElement($reference);
            $reference->removeItem($this);
        }
        return $this;
    }
    #[Serializer\Groups(['read:product-family:option'])]
    public function getText(): ?string {
        return $this->getName();
    }
}
