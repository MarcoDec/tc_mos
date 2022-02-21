<?php

namespace App\Entity\Project\Product;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Family as AbstractFamily;
use App\Filter\OldRelationFilter;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: OldRelationFilter::class, properties: ['parent']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['customsCode' => 'partial', 'name' => 'partial']),
    ApiResource(
        description: 'Famille de produit',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les familles de produit',
                    'summary' => 'Récupère les familles de produit',
                ]
            ],
            'post' => [
                'controller' => PlaceholderAction::class,
                'input_formats' => ['multipart'],
                'openapi_context' => [
                    'description' => 'Créer une famille de produit',
                    'summary' => 'Créer une famille de produit',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une famille de produit',
                    'summary' => 'Supprime une famille de produit',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'post' => [
                'controller' => PlaceholderAction::class,
                'input_formats' => ['multipart'],
                'method' => 'POST',
                'openapi_context' => [
                    'description' => 'Modifie une famille de produit',
                    'summary' => 'Modifie une famille de produit',
                ],
                'path' => '/product-families/{id}',
                'status' => 200
            ]
        ],
        shortName: 'ProductFamily',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PROJECT_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:family', 'write:file', 'write:name'],
            'openapi_definition_name' => 'ProductFamily-write'
        ],
        normalizationContext: [
            'groups' => ['read:family', 'read:file', 'read:id', 'read:name'],
            'openapi_definition_name' => 'ProductFamily-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity,
    ORM\Table(name: 'product_family'),
    UniqueEntity(['name', 'parent'])
]
class Family extends AbstractFamily {
    /** @var Collection<int, self> */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['remove'])]
    protected Collection $children;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Faisceaux'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    /** @var null|self */
    #[
        ApiProperty(description: 'Famille parente', readableLink: false, example: '/api/product-families/1'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    protected $parent;

    #[
        ApiProperty(description: 'Icône', example: '/uploads/product-families/1.jpg'),
        Serializer\Groups(['read:file'])
    ]
    final public function getFilepath(): ?string {
        return parent::getFilepath();
    }
}
