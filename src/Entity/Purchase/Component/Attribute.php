<?php

namespace App\Entity\Purchase\Component;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Unit;
use App\Entity\Traits\NameTrait;
use App\Filter\RelationFilter;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'name' => 'partial',
        'description' => 'partial'
    ]),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'families' => 'name',
        'unit' => 'name'
    ]),
    ApiFilter(filterClass: OrderFilter::class, properties: [
        'name',
        'description'
    ]),
    ApiResource(
        description: 'Attribut',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les attributs',
                    'summary' => 'Récupère les attributs',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un attribut',
                    'summary' => 'Créer un attribut',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un attribut',
                    'summary' => 'Supprime un attribut',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un attribut',
                    'summary' => 'Modifie un attribut',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:attribute', 'write:name', 'write:family', 'write:unit'],
            'openapi_definition_name' => 'Attribute-write'
        ],
        normalizationContext: [
            'groups' => ['read:attribute', 'read:id', 'read:name', 'read:family', 'read:unit'],
            'openapi_definition_name' => 'Attribute-read'
        ],
    ),
    ORM\Entity,
    UniqueEntity('name'),
]
class Attribute extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Longueur'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Nom', required: false, example: 'Longueur de l\'embout'),
        Assert\Length(min: 3, max: 255),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:attribute', 'write:attribute'])
    ]
    private ?string $description = null;

    /**
     * @var Collection<int, Family>
     */
    #[
        ApiProperty(description: 'Famille', required: true, readableLink: false, example: ['/api/component-families/7', '/api/component-families/15']),
        ORM\ManyToMany(fetch: 'EXTRA_LAZY', targetEntity: Family::class),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    private Collection $families;

    #[
        ApiProperty(description: 'Unité', required: false, readableLink: false, example: '/api/units/7'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Unit::class),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    private ?Unit $unit;

    public function __construct() {
        $this->families = new ArrayCollection();
    }

    public function addFamily(Family $family): self {
        if (!$this->families->contains($family)) {
            $this->families[] = $family;
        }

        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @return Collection<int, Family>
     */
    final public function getFamilies(): Collection {
        return new ArrayCollection(
            collect($this->families)
                ->map(static fn (Family $family): array => $family->getChildrenWithSelf()->getValues())
                ->collapse()
                ->values()
                ->all()
        );
    }

    public function getUnit(): ?Unit {
        return $this->unit;
    }

    public function removeFamily(Family $family): self {
        $this->families->removeElement($family);

        return $this;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;

        return $this;
    }

    public function setUnit(?Unit $unit): self {
        $this->unit = $unit;

        return $this;
    }
}
