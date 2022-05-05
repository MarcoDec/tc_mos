<?php

namespace App\Entity\Project\Product;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Family as AbstractFamily;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
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
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_ADMIN.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une famille de produit',
                    'summary' => 'Supprime une famille de produit',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_ADMIN.'\')'
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
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_ADMIN.'\')',
                'status' => 200
            ]
        ],
        shortName: 'ProductFamily',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PROJECT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:family', 'write:file'],
            'openapi_definition_name' => 'ProductFamily-write'
        ],
        normalizationContext: [
            'groups' => ['read:family', 'read:file', 'read:id'],
            'openapi_definition_name' => 'ProductFamily-read',
            'skip_null_values' => false
        ],
        paginationEnabled: false
    ),
    ORM\Entity,
    ORM\Table(name: 'product_family'),
    UniqueEntity(['name', 'parent'])
]
class Family extends AbstractFamily {
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['remove'])]
    protected Collection $children;

    #[
        ApiProperty(description: 'Nom', example: 'Faisceaux'),
        Assert\Length(min: 3, max: 20),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(['read:family', 'write:family'])
    ]
    protected ?string $name = null;

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
