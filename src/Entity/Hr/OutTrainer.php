<?php

namespace App\Entity\Hr;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['firstname' => 'partial', 'lastname' => 'partial', 'address' => 'partial']),
    ApiResource(
        description: 'OutTrainer',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les OutTrainers',
                    'summary' => 'Récupère les OutTrainers',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer OutTrainer',
                    'summary' => 'Créer OutTrainer',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime OutTrainer',
                    'summary' => 'Supprime OutTrainer',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie OutTrainer',
                    'summary' => 'Modifie OutTrainer',
                ]
            ]
        ],
        shortName: 'OutTrainer',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:outtrainer', 'write:name'],
            'openapi_definition_name' => 'OutTrainer-write'
        ],
        normalizationContext: [
            'groups' => ['read:outtrainer', 'read:id', 'read:name'],
            'openapi_definition_name' => 'OutTrainer-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(),
    ORM\Table(name: 'OutTrainer')
]
class OutTrainer extends Entity {
    #[
        ORM\Column(nullable: true),
        Serializer\Groups(['read:outtrainer', 'write:outtrainer']),
        Assert\NotBlank

    ]
    private ?string $firstname = null;

    #[
        ORM\Column(nullable: true),
        Serializer\Groups(['read:Outtrainer', 'write:outtrainer']),
        Assert\NotBlank
        ]
    private ?string $lastname = null;

    #[
        ORM\Column(nullable: true),
        Serializer\Groups(['read:outtrainer', 'write:outtrainer'])
        ]
    private ?string $address = null;



    
    public function getFirstname()
    {
        return $this->firstname;
    }

    
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

   
    public function getLastname()
    {
        return $this->lastname;
    }

    
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

   
    public function getAddress()
    {
        return $this->address;
    }

    
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }
}
