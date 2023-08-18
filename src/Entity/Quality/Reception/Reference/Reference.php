<?php

namespace App\Entity\Quality\Reception\Reference;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Quality\Reception\Check\CheckType;
use App\Doctrine\DBAL\Types\Quality\Reception\Check\KindType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Entity\Quality\Reception\Reference\Management\CompanyReference;
use App\Entity\Quality\Reception\Reference\Purchase\ComponentReference;
use App\Entity\Quality\Reception\Reference\Purchase\FamilyReference as ComponentFamilyReference;
use App\Entity\Quality\Reception\Reference\Purchase\SupplierReference;
use App\Entity\Quality\Reception\Reference\Selling\FamilyReference as ProductFamilyReference;
use App\Entity\Quality\Reception\Reference\Selling\ProductReference;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template I of \App\Entity\Management\Society\Company\Company|\App\Entity\Purchase\Component\Component|\App\Entity\Purchase\Component\Family|\App\Entity\Purchase\Supplier\Supplier|\App\Entity\Project\Product\Family|\App\Entity\Project\Product\Product
 */
#[
    ApiResource(
        description: 'Définition d\'un contrôle réception',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les contrôles réceptions',
                    'summary' => 'Récupère les contrôles réceptions'
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un contrôle réception',
                    'summary' => 'Supprime un contrôle réception'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un contrôle réception',
                    'summary' => 'Modifie un contrôle réception'
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
    ORM\DiscriminatorColumn(name: 'type', type: 'check'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE')
]
abstract class Reference extends Entity implements MeasuredInterface {
    final public const TYPES = [
        CheckType::TYPE_COMPANY => CompanyReference::class,
        CheckType::TYPE_COMPONENT => ComponentReference::class,
        CheckType::TYPE_COMPONENT_FAMILY => ComponentFamilyReference::class,
        CheckType::TYPE_PRODUCT => ProductReference::class,
        CheckType::TYPE_PRODUCT_FAMILY => ProductFamilyReference::class,
        CheckType::TYPE_SUPPLIER => SupplierReference::class
    ];

    /** @var Collection<int, I> */
    protected Collection $items;

    #[
        ApiProperty(description: 'Type', example: KindType::TYPE_QTE, openapiContext: ['enum' => KindType::TYPES]),
        ORM\Column(type: 'check_kind', options: ['default' => KindType::TYPE_QTE]),
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    private ?string $kind = KindType::TYPE_QTE;

    #[
        ApiProperty(description: 'Nom ', required: true, example: 'Dimensions'),
        ORM\Column(length: 100),
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Nombre d\'échantillon', example: '3'),
        ORM\Column(type: 'integer', nullable: true),
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    private ?int $sampleQuantity;

    #[
        ApiProperty(description: 'Valeur Minimale', openapiContext: ['$ref' => '#/components/schemas/Measure-generic']),
        ORM\Embedded,
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    private Measure $minValue;
    #[
        ApiProperty(description: 'Valeur Maximale', openapiContext: ['$ref' => '#/components/schemas/Measure-generic']),
        ORM\Embedded,
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    private Measure $maxValue;
    public function __construct() {
        $this->items = new ArrayCollection();
        $this->minValue = new Measure();
        $this->maxValue = new Measure();
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

    /**
     * @return int
     */
    public function getSampleQuantity(): ?int
    {
        return $this->sampleQuantity;
    }

    /**
     * @param ?int $sampleQuantity
     * @return Reference
     */
    public function setSampleQuantity(?int $sampleQuantity): Reference
    {
        $this->sampleQuantity = $sampleQuantity;
        return $this;
    }

    /**
     * @return Measure
     */
    public function getMinValue(): Measure
    {
        return $this->minValue;
    }

    /**
     * @param Measure $minValue
     * @return Reference
     */
    public function setMinValue(Measure $minValue): Reference
    {
        $this->minValue = $minValue;
        return $this;
    }

    /**
     * @return Measure
     */
    public function getMaxValue(): Measure
    {
        return $this->maxValue;
    }

    /**
     * @param Measure $maxValue
     * @return Reference
     */
    public function setMaxValue(Measure $maxValue): Reference
    {
        $this->maxValue = $maxValue;
        return $this;
    }

    public function getMeasures(): array
    {
        return [$this->minValue, $this->maxValue];
    }

    public function getUnit(): ?Unit
    {
        return null;
    }
}
