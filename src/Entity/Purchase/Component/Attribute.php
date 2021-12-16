<?php

namespace App\Entity\Purchase\Component;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Purchase\Component\Family;
use App\Entity\Management\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Entity\Traits\NameTrait;


#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'name' => 'partial',
        'description' => 'partial',
        'families.name' => 'partial',
        'unit.name' => 'partial'
    ]),
    ApiFilter(OrderFilter::class, properties: [
        'name',
        'description',
        'families.name',
        'unit.name'
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
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
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
            'openapi_definition_name' => 'ComponentAttribute-write'
        ],
        normalizationContext: [
            'groups' => ['read:attribute', 'read:id', 'read:name', 'read:family', 'read:unit'],
            'openapi_definition_name' => 'ComponentAttribute-read'
        ],
        shortName: 'ComponentAttribute'
    ),
    ORM\Entity,
    ORM\Table(name: 'component_attribute'),
]
class Attribute extends Entity
{
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

    #[
        ORM\ManyToMany(targetEntity: Family::class),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    private $families;

    #[
        ApiProperty(description: 'Unité', required: false, readableLink: false, example: '/api/units/7'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Unit::class),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    private ?Unit $unit;

    public function __construct()
    {
        $this->families = new ArrayCollection();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Family[]
     */
    public function getFamilies(): Collection
    {
        return $this->families;
    }

    public function addFamily(Family $family): self
    {
        if (!$this->families->contains($family)) {
            $this->families[] = $family;
        }

        return $this;
    }

    public function removeFamily(Family $family): self
    {
        $this->families->removeElement($family);

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }
}
