<?php

namespace App\Entity\Hr;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Traits\AddressTrait;
use App\Entity\Traits\NameTrait;
use App\Filter\EnumFilter;
use Doctrine\ORM\Mapping as ORM;
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
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un formateur extérieur',
                    'summary' => 'Récupère un formateur extérieur',
                ]
            ],
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
    use AddressTrait;
    use NameTrait;

    #[
        ApiProperty(description: 'Prénom', required: true, example: 'Rawaa'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'CHRAIET'),
        Assert\NotBlank,
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:out-trainer', 'write:out-trainer'])
    ]
    private ?string $surname = null;

    final public function getSurname(): ?string {
        return $this->surname;
    }

    final public function setSurname(?string $surname): self {
        $this->surname = $surname;
        return $this;
    }
}
