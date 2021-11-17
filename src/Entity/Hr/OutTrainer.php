<?php

namespace App\Entity\Hr;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Traits\NameTrait;
use App\Filter\EnumFilter;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: EnumFilter::class, id: 'country', properties: ['address.country']),
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
            'groups' => ['write:address', 'write:name', 'write:out-trainer'],
            'openapi_definition_name' => 'OutTrainer-write'
        ],
        normalizationContext: [
            'groups' => ['read:address', 'read:id', 'read:name', 'read:out-trainer'],
            'openapi_definition_name' => 'OutTrainer-read'
        ]
    ),
    ORM\Entity
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
        ApiProperty(description: 'Adresse'),
        ORM\Embedded,
        Serializer\Groups(['read:out-trainer', 'write:out-trainer'])
    ]
    private Address $address;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'CHRAIET'),
        Assert\NotBlank,
        ORM\Column,
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

    final public function getSurname(): ?string {
        return $this->surname;
    }

    final public function setAddress(Address $address): self {
        $this->address = $address;
        return $this;
    }

    final public function setSurname(?string $surname): self {
        $this->surname = $surname;
        return $this;
    }
}
