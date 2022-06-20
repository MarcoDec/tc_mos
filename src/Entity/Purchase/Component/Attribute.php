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
use App\Filter\RelationFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['unit']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['description' => 'partial', 'name' => 'partial']),
    ApiFilter(filterClass: OrderFilter::class, properties: ['name', 'unit.name']),
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
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
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
            'groups' => ['write:attribute'],
            'openapi_definition_name' => 'Attribute-write'
        ],
        normalizationContext: [
            'groups' => ['read:attribute', 'read:id'],
            'openapi_definition_name' => 'Attribute-read'
        ],
    ),
    ORM\Entity
]
class Attribute extends Entity {
    #[
        ApiProperty(description: 'Nom', required: false, example: 'Longueur de l\'embout'),
        Assert\Length(min: 3, max: 255),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:attribute', 'write:attribute'])
    ]
    private ?string $description = null;

    /** @var Collection<int, Family> */
    #[
        ApiProperty(description: 'Famille', readableLink: false, required: true, example: ['/api/component-families/1', '/api/component-families/2']),
        ORM\ManyToMany(targetEntity: Family::class),
        Serializer\Groups(['read:attribute', 'write:attribute'])
    ]
    private Collection $families;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Longueur'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:attribute', 'write:attribute'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Unité', readableLink: false, required: false, example: '/api/units/1'),
        ORM\ManyToOne(fetch: 'EAGER'),
        Serializer\Groups(['read:attribute', 'write:attribute'])
    ]
    private ?Unit $unit;

    public function __construct() {
        $this->families = new ArrayCollection();
    }

    final public function addFamily(Family $family): self {
        if (!$this->families->contains($family)) {
            $this->families->add($family);
        }
        return $this;
    }

    final public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @return Collection<int, Family>
     */
    final public function getFamilies(): Collection {
        return $this->families;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getUnit(): ?Unit {
        return $this->unit;
    }

    final public function removeFamily(Family $family): self {
        if ($this->families->contains($family)) {
            $this->families->removeElement($family);
        }
        return $this;
    }

    final public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setUnit(?Unit $unit): self {
        $this->unit = $unit;
        return $this;
    }
}
