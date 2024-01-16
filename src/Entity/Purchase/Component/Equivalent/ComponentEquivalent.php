<?php

namespace App\Entity\Purchase\Component\Equivalent;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Entity\Purchase\Component\Family;
use App\Entity\Traits\BarCodeTrait;
use App\Filter\RelationFilter;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['family', 'name']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['family', 'unit']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'code' => 'partial', 'family' => 'exact', 'unit' => 'exact']),
    ApiResource(
        description: 'Groupe d\'équivalence',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:component-equivalent:collection', 'read:id'],
                    'openapi_definition_name' => 'ComponentEquivalent-collection',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les groupes d\'équivalence composants',
                    'summary' => 'Récupère les groupes d\'équivalence composants'
                ]
            ],
            'options' => [
                'controller' => PlaceholderAction::class,
                'filters' => [],
                'method' => 'GET',
                'normalization_context' => [
                    'groups' => ['read:id', 'read:component-equivalent:option'],
                    'openapi_definition_name' => 'ComponentEquivalent-options',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les composants pour les select',
                    'summary' => 'Récupère les groupes d\'équivalence composants pour les select',
                ],
                'order' => ['id' => 'asc'],
                'pagination_enabled' => false,
                'path' => '/component-equivalents/options'
            ],
            'post' => [
                'denormalization_context' => [
                    'groups' => ['create:component-equivalent'],
                    'openapi_definition_name' => 'ComponentEquivalent-create'
                ],
                'openapi_context' => [
                    'description' => 'Créer un groupe d\'équivalence composant',
                    'summary' => 'Créer un groupe d\'équivalence composant'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')',
                'validation_groups' => ['ComponentEquivalent-create']
            ]
        ],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un groupe d\'équivalence composant',
                    'summary' => 'Récupère un groupe d\'équivalence composant',
                ],
                'normalization_context' => [
                    'groups' => ['read:component-equivalent', 'read:id'],
                    'openapi_definition_name' => 'ComponentEquivalent-read',
                    'skip_null_values' => false
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un groupe d\'équivalence composant',
                    'summary' => 'Modifie un groupe d\'équivalence composant',
                ],
                'denormalization_context' => [
                    'groups' => ['write:component-equivalent'],
                    'openapi_definition_name' => 'ComponentEquivalent-write'
                ]
            ],
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un groupe d\'équivalence composant',
                    'summary' => 'Supprime un groupe d\'équivalence composant'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => [
                'create:component-equivalent',
                'write:component-equivalent'
            ],
            'openapi_definition_name' => 'ComponentEquivalent-write'
        ],
        normalizationContext: [
            'groups' => [
                'read:component-equivalent',
                'read:component-equivalent:collection',
                'read:id'
            ],
            'openapi_definition_name' => 'ComponentEquivalent-read',
            'skip_null_values' => false
        ],
        paginationClientEnabled: true
    ),
    ORM\Entity()
    ]
class ComponentEquivalent extends Entity implements BarCodeInterface, MeasuredInterface
{
    use BarCodeTrait;
    #[
        ApiProperty(description: 'Famille du Groupe d\'équivalence', readableLink: false, required: true, example: '/api/component-families/1'),
        Assert\NotBlank(groups: ['ComponentEquivalent-admin', 'ComponentEquivalent-create']),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne(targetEntity: Family::class, fetch: 'EAGER', inversedBy: 'components'),
        Serializer\Groups(['create:component-equivalent', 'read:component-equivalent', 'read:component-equivalent:collection', 'write:component-equivalent', 'write:component-equivalent:admin'])
    ]
    private ?Family $family = null;
    #[
        ApiProperty(description: 'Nom', required: true, example: 'Equivalents SCOTCH ADHESIF PVC T2 19MMX33M NOIR'),
        Assert\NotBlank(groups: ['ComponentEquivalent-admin', 'ComponentEquivalent-create']),
        ORM\Column,
        Serializer\Groups(['create:component-equivalent', 'read:component-equivalent', 'read:component-equivalent:collection', 'write:component-equivalent', 'write:component-equivalent:admin', 'read:id'])
    ]
    private ?string $name = null;
    #[
        ApiProperty(description: 'Description', required: false, example: 'Equivalents Stellantis'),
        Assert\NotBlank(groups: ['ComponentEquivalent-admin', 'ComponentEquivalent-create']),
        ORM\Column,
        Serializer\Groups(['create:component-equivalent', 'read:component-equivalent', 'read:component-equivalent:collection', 'write:component-equivalent', 'write:component-equivalent:admin', 'read:id'])
    ]
    private ?string $description = null;
    #[
        ApiProperty(description: 'Unité', readableLink: false, required: false, example: '/api/units/1'),
        Assert\NotBlank(groups: ['ComponentEquivalent-create', 'ComponentEquivalent-logistics']),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne(fetch:'EAGER'),
        Serializer\Groups(['read:component-equivalent:collection', 'create:component-equivalent', 'read:component-equivalent', 'write:component-equivalent', 'write:component-equivalent:logistics'])
    ]
    private ?Unit $unit = null;
    #[
        ApiProperty(description: 'Référence interne', required: true, example: 'EQU-1'),
        ORM\Column,
        Serializer\Groups(['read:component-equivalent', 'read:component-equivalent:collection'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Items equivalents', readableLink: true, required: true, example: '[`/api/component-equivalent-items/1`, `/api/component-equivalent-items/2`]'),
        ORM\OneToMany(mappedBy: 'componentEquivalent', targetEntity: ComponentEquivalentItem::class, cascade: ['persist', 'remove'], fetch: 'EAGER', orphanRemoval: true),
        Serializer\Groups(['read:component-equivalent', 'read:component-equivalent:collection', 'write:component-equivalent'])
    ]
    private Collection $items;

    public function __construct()
    {
        $this->generateCode();
        $this->items = new ArrayCollection();
    }

    public static function getBarCodeTableNumber(): string
    {
        return self::EQUIVALENT_BAR_CODE_TABLE_NUMBER;
    }

    public function getMeasures(): array
    {
        return [];
    }
    public function getUnitMeasures(): array
    {
        return [];
    }
    public function getCurrencyMeasures(): array
    {
        return [];
    }

    #[Serializer\Groups(['read:component-equivalent:option'])]
    final public function getText(): ?string
    {
        return $this->getCode();
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function generateCode()
    {
        $this->code = 'EQ-'.$this->family?->getCode().'-'.$this->getId();
    }

    public function getFamily(): ?Family
    {
        return $this->family;
    }

    public function setFamily(?Family $family): ComponentEquivalent
    {
        $this->family = $family;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): ComponentEquivalent
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): ComponentEquivalent
    {
        $this->description = $description;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): ComponentEquivalent
    {
        $this->code = $code;
        return $this;
    }
    public function setUnit(?Unit $unit): ComponentEquivalent
    {
        $this->unit = $unit;
        return $this;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function setItems(Collection $items): ComponentEquivalent
    {
        $this->items = $items;
        return $this;
    }
    #[Serializer\Groups(['read:component-equivalent', 'read:component-equivalent:collection', 'write:component-equivalent'])]
    public function getComponents(): Collection
    {
        $components = new ArrayCollection();
        foreach ($this->items as $item) {
            if ($item->isDeleted()) {
                continue;
            } else {
                $components->add($item->getComponent());
            }
        }
        return $components;
    }
}