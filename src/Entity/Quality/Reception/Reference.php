<?php

namespace App\Entity\Quality\Reception;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\ItemType;
use App\Doctrine\DBAL\Types\Quality\Reception\CheckType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template F of \App\Entity\Purchase\Component\Family|\App\Entity\Project\Product\Family
 * @template I of \App\Entity\Purchase\Component\Component|\App\Entity\Project\Product\Product
 */
#[
    ApiResource(
        description: 'Définition d\'un contrôle réception',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les contrôles',
                    'summary' => 'Récupère les contrôles'
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un contrôle',
                    'summary' => 'Supprime un contrôle'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un contrôle',
                    'summary' => 'Modifie un contrôle'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_ADMIN.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_QUALITY_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:reference'],
            'openapi_definition_name' => 'Reference-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:reference'],
            'openapi_definition_name' => 'Reference-read',
            'skip_null_values' => false
        ]
    ),
    ORM\DiscriminatorColumn(name: 'type', type: 'item'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE')
]
abstract class Reference extends Entity {
    final public const TYPES = [ItemType::TYPE_COMPONENT => ComponentReference::class, ItemType::TYPE_PRODUCT => ProductReference::class];

    /** @var Collection<int, F> */
    protected Collection $families;

    /** @var Collection<int, I> */
    protected Collection $items;

    #[
        ApiProperty(description: 'Type', example: CheckType::TYPE_QTE, openapiContext: ['enum' => CheckType::TYPES]),
        ORM\Column(type: 'check_kind', options: ['default' => CheckType::TYPE_QTE]),
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    private ?string $kind = CheckType::TYPE_QTE;

    #[
        ApiProperty(description: 'Nom ', required: true, example: 'Dimensions'),
        ORM\Column(length: 100),
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    private ?string $name = null;

    public function __construct() {
        $this->families = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    /**
     * @param F $family
     *
     * @return $this
     */
    final public function addFamily($family): self {
        if (!$this->families->contains($family)) {
            $this->families->add($family);
        }
        return $this;
    }

    /**
     * @param I $item
     *
     * @return $this
     */
    final public function addItem($item): self {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }
        return $this;
    }

    /**
     * @return Collection<int, F>
     */
    final public function getFamilies(): Collection {
        return $this->families;
    }

    /**
     * @return Collection<int, I>
     */
    final public function getItems(): Collection {
        return $this->items;
    }

    final public function getKind(): ?string {
        return $this->kind;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param F $family
     *
     * @return $this
     */
    final public function removeFamily($family): self {
        if ($this->families->contains($family)) {
            $this->families->removeElement($family);
        }
        return $this;
    }

    /**
     * @param I $item
     *
     * @return $this
     */
    final public function removeItem($item): self {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
        }
        return $this;
    }

    /**
     * @return $this
     */
    final public function setKind(?string $kind): self {
        $this->kind = $kind;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
