<?php

namespace App\Entity\Hr;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['address' => 'partial', 'name' => 'partial', 'surname' => 'partial']),
    ApiResource(
        description: 'Formateur extérieur',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les formateurs extérieurs',
                    'summary' => 'Récupère les formateurs extérieurs',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un formateur extérieur',
                    'summary' => 'Créer un formateur extérieur',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un formateur extérieur',
                    'summary' => 'Supprime un formateur extérieur',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un formateur extérieur',
                    'summary' => 'Modifie un formateur extérieur',
                ]
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name', 'write:out-trainer'],
            'openapi_definition_name' => 'OutTrainer-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:out-trainer'],
            'openapi_definition_name' => 'OutTrainer-read'
        ]
    ),
    ORM\Entity,
    ORM\Table
]
class OutTrainer extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Prénom', required: true, example: 'Rawaa'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'address', example: 'RUE IBN KHALDOUN'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:out-trainer', 'write:out-trainer'])
    ]
    private ?string $address = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'CHRAIET'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:out-trainer', 'write:out-trainer'])
    ]
    private ?string $surname = null;

    final public function getAddress(): ?string {
        return $this->address;
    }

    final public function getSurname(): ?string {
        return $this->surname;
    }

    final public function setAddress(?string $address): self {
        $this->address = $address;
        return $this;
    }

    final public function setSurname(?string $surname): self {
        $this->surname = $surname;
        return $this;
    }
}
