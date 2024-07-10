<?php

namespace App\Entity\Quality\Production;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Quality\Production\ComponentReferenceField;
use App\Entity\Entity;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Entity\Purchase\Component\Component;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['component.id']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['component']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['height.value.value' => 'partial', 'height.value.code' => 'partial', 'section.code' => 'exact', 'section.value' => 'partial', 'tensile.value.code' => 'exact', 'tensile.value.value' => 'partial', 'width.value.code' => 'exact', 'width.value.value' => 'partial']),
    ApiResource(
        description: 'Valeur de référence du composant',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les valeurs de référence du composant',
                    'summary' => 'Récupère les valeurs de référence du composant',
                ],
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer la valeur de référence du composant',
                    'summary' => 'Créer la valeur de référence du composant',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime la valeur de référence du composant',
                    'summary' => 'Supprime la valeur de référence du composant',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_ADMIN.'\')'
            ],
            'get',
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie la valeur de référence du composant',
                    'summary' => 'Modifie la valeur de référence du composant',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_WRITER.'\')'
            ],
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_QUALITY_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:component-reference-field', 'write:component-reference-value', 'write:measure'],
            'openapi_definition_name' => 'ComponentReferenceValue-write'
        ],
        normalizationContext: [
            'groups' => ['read:component-reference-field', 'read:component-reference-value', 'read:id', 'read:measure'],
            'openapi_definition_name' => 'ComponentReferenceValue-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity
]
class ComponentReferenceValue extends Entity implements MeasuredInterface {
    #[
        ApiProperty(description: 'Composant', readableLink: false, example: '/api/components/2'),
        ORM\ManyToOne,
        Serializer\Groups(['read:component-reference-value', 'write:component-reference-value'])
    ]
    private Component $component;

    #[
        ApiProperty(description: 'Hauteur'),
        ORM\Embedded,
        Serializer\Groups(['read:component-reference-value', 'write:component-reference-value'])
    ]
    private ComponentReferenceField $height;

    #[
        ApiProperty(description: 'Valeur', openapiContext: ['$ref' => '#/components/schemas/Measure-length']),
        ORM\Embedded,
        Serializer\Groups(['read:component-reference-value', 'write:component-reference-value'])
    ]
    private Measure $section;

    #[
        ApiProperty(description: 'Résistance'),
        ORM\Embedded,
        Serializer\Groups(['read:component-reference-value', 'write:component-reference-value'])
    ]
    private ComponentReferenceField $tensile;

    #[
        ApiProperty(description: 'Largeur'),
        ORM\Embedded,
        Serializer\Groups(['read:component-reference-value', 'write:component-reference-value'])
    ]
    private ComponentReferenceField $width;

    public function __construct() {
        $this->height = new ComponentReferenceField();
        $this->section = new Measure();
        $this->tensile = new ComponentReferenceField();
        $this->width = new ComponentReferenceField();
    }

    final public function getComponent(): Component {
        return $this->component;
    }

    final public function getHeight(): ComponentReferenceField {
        return $this->height;
    }

    final public function getSection(): Measure {
        return $this->section;
    }

    final public function getTensile(): ComponentReferenceField {
        return $this->tensile;
    }

    final public function getWidth(): ComponentReferenceField {
        return $this->width;
    }

    final public function setComponent(Component $component): self {
        $this->component = $component;
        return $this;
    }

    final public function setHeight(ComponentReferenceField $height): self {
        $this->height = $height;
        return $this;
    }

    final public function setSection(Measure $section): self {
        $this->section = $section;
        return $this;
    }

    final public function setTensile(ComponentReferenceField $tensile): self {
        $this->tensile = $tensile;
        return $this;
    }

    final public function setWidth(ComponentReferenceField $width): self {
        $this->width = $width;
        return $this;
    }

    public function getMeasures(): array
    {
        return [$this->section];
    }

    public function getUnitMeasures(): array
    {
        return [$this->section];
    }
    public function getCurrencyMeasures(): array
    {
        return [];
    }

    public function getUnit(): ?Unit
    {
        return null;
    }
}
