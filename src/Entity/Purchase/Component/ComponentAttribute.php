<?php

namespace App\Entity\Purchase\Component;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Purchase\Component\AttributeType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Color;
use App\Entity\Management\Unit;
use App\Filter\RelationFilter;
use App\Repository\Purchase\Component\ComponentAttributeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['component']),
    ApiResource(
        description: 'Caractéristique d\'un composant',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les caractéristiques d\'un composant',
                    'summary' => 'Récupère les caractéristiques d\'un composant',
                ],
            ],
        ],
        itemOperations: [
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une caractéristique d\'un composant',
                    'summary' => 'Modifie une caractéristique d\'un composant',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:component-attribute'],
            'openapi_definition_name' => 'ComponentAttribute-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:component-attribute'],
            'openapi_definition_name' => 'ComponentAttribute-read',
            'skip_null_values' => false
        ],
        paginationClientEnabled: true
    ),
    ORM\Entity(repositoryClass: ComponentAttributeRepository::class),
    ORM\UniqueConstraint(columns: ['attribute_id', 'component_id'])
]
class ComponentAttribute extends Entity implements MeasuredInterface {
    #[
        ApiProperty(description: 'Attribut', readableLink: false, example: '/api/attributes/1'),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne(inversedBy: 'attributes'),
        Serializer\Groups(['read:component-attribute'])
    ]
    private ?Attribute $attribute = null;

    #[
        ApiProperty(description: 'Couleur'),
        ORM\ManyToOne,
        Serializer\Groups(['read:component-attribute', 'write:component-attribute'])
    ]
    private ?Color $color = null;

    #[
        ApiProperty(description: 'Composant', readableLink: false, example: '/api/components/1'),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne(inversedBy: 'attributes'),
        Serializer\Groups(['read:component-attribute'])
    ]
    private ?Component $component = null;

    #[
        ApiProperty(description: 'Valeur mesurable', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:component-attribute', 'write:component-attribute'])
    ]
    private Measure $measure;

    #[
        ApiProperty(description: 'Valeur'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:component-attribute', 'write:component-attribute'])
    ]
    private bool|int|float|string|null $value = null;

    public function __construct() {
        $this->measure = new Measure();
    }

    final public function getAttribute(): ?Attribute {
        return $this->attribute;
    }

    final public function getColor(): ?Color {
        return $this->color;
    }

    final public function getComponent(): ?Component {
        return $this->component;
    }

    final public function getMeasure(): Measure {
        return $this->measure;
    }

    final public function getMeasures(): array {
        return [$this->measure];
    }

    final public function getType(): string {
        return $this->attribute?->getType() ?? AttributeType::TYPE_TEXT;
    }

    final public function getUnit(): ?Unit {
        return $this->attribute?->getUnit();
    }

    final public function getValue(): bool|int|float|string|null {
        return $this->value;
    }

    final public function setAttribute(?Attribute $attribute): self {
        $this->attribute = $attribute;
        return $this;
    }

    final public function setColor(?Color $color): self {
        $this->color = $color;
        return $this;
    }

    final public function setComponent(?Component $component): self {
        $this->component = $component;
        return $this;
    }

    final public function setMeasure(Measure $measure): self {
        $this->measure = $measure;
        return $this;
    }

    final public function setValue(bool|int|float|string|null $value): self {
        $this->value = match ($this->getType()) {
            AttributeType::TYPE_BOOL => (bool) $value,
            AttributeType::TYPE_INT => (int) $value,
            AttributeType::TYPE_PERCENT => (float) $value,
            default => $value,
        };
        return $this;
    }
}
