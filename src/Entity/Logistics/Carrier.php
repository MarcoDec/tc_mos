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
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial','address' => 'partial']),
    ApiResource(
        description: 'Carrier',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les Transporteurs',
                    'summary' => 'Récupère les Transporteurs',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un Transporteur',
                    'summary' => 'Créer un Transporteur',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un Transporteur',
                    'summary' => 'Supprime un Transporteur',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un Transporteur',
                    'summary' => 'Modifie un Transporteur',
                ]
            ]
        ],
        shortName: 'Carrier',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:carrier', 'write:name'],
            'openapi_definition_name' => 'OutTrainer-write'
        ],
        normalizationContext: [
            'groups' => ['read:carrier', 'read:id', 'read:name'],
            'openapi_definition_name' => 'OutTrainer-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(),
    ORM\Table(name: 'Carrier')
]
class Carrier extends Entity {
    #[
        ApiProperty(description: 'nom', example: 'DHL'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name', 'write:name']),
        Assert\NotBlank

    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'address', example: 'null'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:carrier', 'write:carrier'])
        ]
    private ?string $address = null;


   
    public function getAddress(): ?string
    {
        return $this->address;
    }

    
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

     
    public function getName(): ?string
    {
        return $this->name;
    }

   
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}