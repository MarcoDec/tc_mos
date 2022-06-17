<?php

namespace App\Entity\Hr;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['name', 'surname']),
    ApiFilter(filterClass: OrderFilter::class, id: 'address-sorter', properties: Address::sorter),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'surname' => 'partial']),
    ApiFilter(filterClass: SearchFilter::class, id: 'address', properties: Address::filter),
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
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un formateur extérieur',
                    'summary' => 'Supprime un formateur extérieur',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un formateur extérieur',
                    'summary' => 'Modifie un formateur extérieur',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:address', 'write:out-trainer'],
            'openapi_definition_name' => 'OutTrainer-write'
        ],
        normalizationContext: [
            'groups' => ['read:address', 'read:id', 'read:out-trainer'],
            'openapi_definition_name' => 'OutTrainer-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class OutTrainer extends Entity {
    #[
        ApiProperty(description: 'Adresse'),
        ORM\Embedded,
        Serializer\Groups(['read:out-trainer', 'write:out-trainer'])
    ]
    private Address $address;

    #[
        ApiProperty(description: 'Prénom', required: true, example: 'Rawaa'),
        Assert\Length(min: 3, max: 30),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(['read:out-trainer', 'write:out-trainer'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'CHRAIET'),
        Assert\Length(min: 3, max: 30),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(['read:out-trainer', 'write:out-trainer'])
    ]
    private ?string $surname = null;

    #[Pure]
    public function __construct() {
        $this->address = new Address();
    }

    final public function getAddress(): Address {
        return $this->address;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getSurname(): ?string {
        return $this->surname;
    }

    final public function setAddress(Address $address): self {
        $this->address = $address;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setSurname(?string $surname): self {
        $this->surname = $surname;
        return $this;
    }
}
