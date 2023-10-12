<?php

namespace App\Entity\Purchase\Component\Equivalent;

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

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['family', 'name']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['family', 'unit']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'code' => 'partial', 'family' => 'exact', 'unit' => 'exact']),
    ApiResource(
        description: 'Groupe d\'équivalence',
        collectionOperations: [
            'get' => [
                'security' => "is_granted('ROLE_COMPONENT_EQUIVALENT_LIST')"
            ],
            'post' => [
                'security' => "is_granted('ROLE_COMPONENT_EQUIVALENT_CREATE')"
            ]
        ],
        itemOperations: [
            'get' => [
                'security' => "is_granted('ROLE_COMPONENT_EQUIVALENT_SHOW')"
            ],
            'put' => [
                'security' => "is_granted('ROLE_COMPONENT_EQUIVALENT_UPDATE')"
            ],
            'delete' => [
                'security' => "is_granted('ROLE_COMPONENT_EQUIVALENT_DELETE')"
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
                'read:component-equivalent:collection'
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

    public function __construct()
    {
        $this->code = $this->generateCode();
    }

    public static function getBarCodeTableNumber(): string
    {
        return self::EQUIVALENT_BAR_CODE_TABLE_NUMBER;
    }

    public function getMeasures(): array
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

    private function generateCode()
    {
        // TODO: generate code
        return '';
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

}