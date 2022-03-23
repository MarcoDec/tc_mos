<?php

namespace App\Entity\Hr;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\ApiPlatform\Core\Annotation\ApiProperty;
use App\ApiPlatform\Core\Annotation\ApiSerializerGroups;
use App\Entity\Embeddable\Address;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formateur extérieur.
 */
#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'surname' => 'partial']),
    ApiFilter(filterClass: SearchFilter::class, id: 'address', properties: Address::filter),
    ApiResource(
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
        denormalizationContext: [
            'groups' => ['OutTrainer-write'],
            'openapi_definition_name' => 'OutTrainer-write'
        ],
        normalizationContext: [
            'groups' => ['OutTrainer-read'],
            'openapi_definition_name' => 'OutTrainer-read'
        ]
    ),
    ApiSerializerGroups(inheritedRead: ['OutTrainer-read' => ['OutTrainer-write', 'Entity']], write: ['OutTrainer-write']),
    ORM\Entity
]
class OutTrainer extends Entity {
    #[
        Assert\Valid,
        ApiProperty(readRef: 'Address-read', writeRef: 'Address-write'),
        ORM\Embedded,
        Serializer\Groups(groups: ['OutTrainer-read', 'OutTrainer-write'])
    ]
    private Address $address;

    /**
     * @var null|string Prénom
     */
    #[
        ApiProperty(example: 'Rawaa', format: 'name'),
        Assert\Length(min: 3, max: 30),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(groups: ['OutTrainer-read', 'OutTrainer-write'])
    ]
    private ?string $name = null;

    /**
     * @var null|string Nom
     */
    #[
        ApiProperty(example: 'CHRAIET', format: 'name'),
        Assert\Length(min: 3, max: 30),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(groups: ['OutTrainer-read', 'OutTrainer-write'])
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
