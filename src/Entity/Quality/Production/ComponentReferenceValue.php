<?php

namespace App\Entity\Quality\Production;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Quality\Production\ComponentReferenceField;
use App\Entity\Entity;
use App\Entity\Purchase\Component\Component;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
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
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère la valeur de référence du composant',
                    'summary' => 'Récupère la valeur de référence du composant',
                ]
            ],
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
            'groups' => ['write:component', 'write:component-reference-value', 'write:component-reference-field'],
            'openapi_definition_name' => 'ComponentReferenceValue-write'
        ],
        normalizationContext: [
            'groups' => ['read:component', 'read:component-reference-value', 'read:component-reference-field'],
            'openapi_definition_name' => 'ComponentReferenceValue-read'
        ],
    ),
    ORM\Entity
]
class ComponentReferenceValue extends Entity {
    #[
        ApiProperty(description: 'Composant', readableLink: false, example: '/api/components/2'),
        ORM\ManyToOne(targetEntity: Component::class, fetch: 'EAGER'),
        Serializer\Groups(['read:component', 'write:component'])
    ]
    private Component $component;

    #[
        ApiProperty(description: 'Hauteur'),
        ORM\Embedded,
        Serializer\Groups(['read:component-reference-value', 'write:component-reference-value'])
    ]
    private ComponentReferenceField $height;

    #[
        ApiProperty(description: 'Valeur', example: 0),
        ORM\Column(options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:component-reference-value', 'write:component-reference-value'])
    ]
    private float $section = 0;

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
        $this->tensile = new ComponentReferenceField();
        $this->width = new ComponentReferenceField();
    }

    public function getComponent(): Component {
        return $this->component;
    }

    final public function getHeight(): ComponentReferenceField {
        return $this->height;
    }

    final public function getSection(): float {
        return $this->section;
    }

    final public function getTensile(): ComponentReferenceField {
        return $this->tensile;
    }

    final public function getWidth(): ComponentReferenceField {
        return $this->width;
    }

    final public function isHeightValid(float $value): bool {
        return $this->height->isValid($value);
    }

    final public function isTensileValid(float $value): bool {
        return $this->tensile->isValid($value);
    }

    final public function isWidthValid(float $value): bool {
        return $this->width->isValid($value);
    }

    public function setComponent(Component $component): self {
        $this->component = $component;
        return $this;
    }

    final public function setHeight(ComponentReferenceField $height): self {
        $this->height = $height;
        return $this;
    }

    final public function setSection(float $section): self {
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
}
