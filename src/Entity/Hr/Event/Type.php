<?php

namespace App\Entity\Hr\Event;

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
    
    ApiFilter(filterClass: SearchFilter::class, properties: [ 'name' => 'partial', 'toStatus' => 'partial']), 
    ApiResource(
        description: "type d'événement",
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => "Récupère les types d'événement ",
                    'summary' => "Récupère les types d'événement",
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => "Créer un type d'événement",
                    'summary' => "Créer un type d'événement",
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => "Supprime un type d'événement",
                    'summary' => "Supprime un type d'événement",
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => "Modifie un type d'événement",
                    'summary' => "Modifie un type d'événement",
                ]
            ] 
        ],
        shortName: 'EventType',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:type', 'write:name'],
            'openapi_definition_name' => 'EventType-write'
        ],
        normalizationContext: [
            'groups' => ['read:type', 'read:id', 'read:name'],
            'openapi_definition_name' => 'EventType-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(),
    ORM\Table(name: 'event_type')
]
class Type extends Entity {
    #[
        ApiProperty(description: 'Status', example: 'NULL'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:type', 'write:type'])

    ]
    private ?string $toStatus = null;


    #[
        ApiProperty(description: 'Nom', required: true, example: 'ABSENCE'),
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name']),
        Assert\NotBlank
        ]
    private ?string $name;

   
    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    
    public function getToStatus(): ?string 
    {
        return $this->toStatus;
    }

    
    public function setToStatus(?string $toStatus): self
    {
        $this->toStatus = $toStatus;

        return $this;
    }
}
