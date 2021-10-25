<?php

namespace App\Entity\Purchase\Component;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['parent']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['customsCode' => 'partial', 'name' => 'partial', 'code' => 'partial', 'copperable' => 'exact']),
    ApiResource(
        description: 'Famille de composant',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les familles de composant',
                    'summary' => 'Récupère les familles de composant',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une famille de composant',
                    'summary' => 'Créer une famille de composant',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une famille de composant',
                    'summary' => 'Supprime une famille de composant',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une famille de composant',
                    'summary' => 'Modifie une famille de composant',
                ]
            ]
        ],
        shortName: 'ComponentFamily',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:family', 'write:name'],
            'openapi_definition_name' => 'ComponentFamily-write'
        ],
        normalizationContext: [
            'groups' => ['read:family', 'read:id', 'read:name'],
            'openapi_definition_name' => 'ComponentFamily-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(),
    ORM\Table(name: 'component_family')
]
class Family extends Entity {
    #[
        ApiProperty(description: 'Code ', example: 'CAB'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:family', 'write:family'])

    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'cuivré ', example: 'True'),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:family', 'write:family'])
        ]
    private bool $copperable = false;

    #[
        ApiProperty(description: 'Code douanier', example: '8544300089'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:family', 'write:family'])
        ]
    private ?string $customsCode = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Faisceaux'),
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name']),
        Assert\NotBlank
        ]
    private ?string $name;

    #[
        ApiProperty(description: 'Famille parente', readableLink: false, example: '/api/component-families/2'),
        ORM\ManyToOne,
        Serializer\Groups(['read:family', 'write:family'])
        ]
    private ?Family $parent = null;

    public function getCode(): ?string {
        return $this->code;
    }

    public function getCopperable(): ?bool {
        return $this->copperable;
    }

    public function getCustomsCode(): ?string {
        return $this->customsCode;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getParent(): ?self {
        return $this->parent;
    }

    public function setCode(?string $code): self {
        $this->code = $code;

        return $this;
    }

    public function setCopperable(bool $copperable): self {
        $this->copperable = $copperable;

        return $this;
    }

    public function setCustomsCode(string $customsCode): self {
        $this->customsCode = $customsCode;

        return $this;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function setParent(?self $parent): self {
        $this->parent = $parent;

        return $this;
    }
}
