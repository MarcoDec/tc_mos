<?php

namespace App\Entity\Logistics;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial','ral' => 'partial','rgb' => 'partial']),
    ApiResource(
        description: 'Color',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les Couleurs',
                    'summary' => 'Récupère les Couleurs',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un Couleur',
                    'summary' => 'Créer un Couleur',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un Couleur',
                    'summary' => 'Supprime un Couleur',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un Couleur',
                    'summary' => 'Modifie un Couleur',
                ]
            ]
        ],
        shortName: 'Color',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:color', 'write:name'],
            'openapi_definition_name' => 'Color-write'
        ],
        normalizationContext: [
            'groups' => ['read:color', 'read:id', 'read:name'],
            'openapi_definition_name' => 'Color-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(),
    ORM\Table(name: 'Color')
]
class Color extends Entity {
    #[
        ApiProperty(description: 'nom', example: 'Gris'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name', 'write:name']),
        Assert\NotBlank

    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'ral', example: '17122018'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:color', 'write:color'])
        ]
    private ?string $ral = null;

    #[
        ApiProperty(description: 'rgb', example: '#848484'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:color', 'write:color'])
        ]
    private ?string $rgb = null;


     
    public function getName()
    {
        return $this->name;
    }

   
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    
    public function getRal()
    {
        return $this->ral;
    }

    
    public function setRal($ral)
    {
        $this->ral = $ral;

        return $this;
    }

    
    public function getRgb()
    {
        return $this->rgb;
    }

   
    public function setRgb($rgb)
    {
        $this->rgb = $rgb;

        return $this;
    }
}