<?php

namespace App\Entity\Purchase\Component;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Family as AbstractFamily;
use App\Filter\RelationFilter;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['copperable']),
    ApiFilter(filterClass: RelationFilter::class, properties: ['parent']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['customsCode' => 'partial', 'name' => 'partial', 'code' => 'partial']),
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
                'controller' => PlaceholderAction::class,
                'input_formats' => ['multipart'],
                'openapi_context' => [
                    'description' => 'Créer une famille de composant',
                    'summary' => 'Créer une famille de composant',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une famille de composant',
                    'summary' => 'Supprime une famille de composant',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'post' => [
                'controller' => PlaceholderAction::class,
                'input_formats' => ['multipart'],
                'method' => 'POST',
                'openapi_context' => [
                    'description' => 'Modifie une famille de composant',
                    'summary' => 'Modifie une famille de composant',
                ],
                'path' => '/component-families/{id}',
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')',
                'status' => 200
            ]
        ],
        shortName: 'ComponentFamily',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:family', 'write:file'],
            'openapi_definition_name' => 'ComponentFamily-write'
        ],
        normalizationContext: [
            'groups' => ['read:family', 'read:file', 'read:id'],
            'openapi_definition_name' => 'ComponentFamily-read',
            'skip_null_values' => false
        ],
        paginationEnabled: false
    ),
    ORM\Entity,
    ORM\Table(name: 'component_family'),
    UniqueEntity(['name', 'parent'])
]
class Family extends AbstractFamily {
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['remove'])]
    protected Collection $children;

    #[
        ApiProperty(description: 'Nom', example: 'Câbles'),
        Assert\Length(min: 3, max: 20),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Famille parente', readableLink: false, example: '/api/component-families/2'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    protected $parent;

    #[
        ApiProperty(description: 'Code ', example: 'CAB'),
        Assert\Length(exactly: 3),
        Assert\NotBlank,
        ORM\Column(type: 'char', length: 3, options: ['charset' => 'ascii']),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Cuivré ', example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    private bool $copperable = false;

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getCopperable(): ?bool {
        return $this->copperable;
    }

    #[
        ApiProperty(description: 'Icône', example: '/uploads/component-families/1.jpg'),
        Serializer\Groups(['read:file'])
    ]
    final public function getFilepath(): ?string {
        return parent::getFilepath();
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setCopperable(bool $copperable): self {
        $this->copperable = $copperable;
        return $this;
    }
}
