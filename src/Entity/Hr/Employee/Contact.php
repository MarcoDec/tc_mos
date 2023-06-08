<?php

namespace App\Entity\Hr\Employee;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['employee']),
    ApiResource(
        description: 'Contact',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les contacts',
                    'summary' => 'Récupère les contacts',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un contact',
                    'summary' => 'Créer un contact',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un contact',
                    'summary' => 'Supprime un contact',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un contact',
                    'summary' => 'Modifie un contact',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')'
            ]
        ],
        shortName: 'EmployeeContact',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:employee-contact'],
            'openapi_definition_name' => 'EmployeeContact-write'
        ],
        normalizationContext: [
            'groups' => ['read:employee-contact', 'read:id'],
            'openapi_definition_name' => 'EmployeeContact-read',
            'skip_null_values' => false
        ],
        paginationEnabled: false
    ),
    ORM\Entity,
    ORM\Table(name: 'employee_contact')
]
class Contact extends Entity {
    #[
        ApiProperty(description: 'Employé', example: '/api/employees/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:employee-contact', 'write:employee-contact'])
    ]
    private ?Employee $employee = null;

    #[
        ApiProperty(description: 'Prénom', example: 'Charles'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee-contact', 'write:employee-contact'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Numéro de téléphone', required: false, example: '03 84 91 99 84'),
        ORM\Column(length: 18, nullable: true),
        Serializer\Groups(['read:employee-contact', 'write:employee-contact'])
    ]
    private ?string $phone = null;

    #[
        ApiProperty(description: 'Prénom', example: 'De Gaule'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee-contact', 'write:employee-contact'])
    ]
    private ?string $surname = null;

    final public function getEmployee(): ?Employee {
        return $this->employee;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getPhone(): ?string {
        return $this->phone;
    }

    final public function getSurname(): ?string {
        return $this->surname;
    }

    final public function setEmployee(?Employee $employee): self {
        $this->employee = $employee;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setPhone(?string $phone): self {
        $this->phone = $phone;
        return $this;
    }

    final public function setSurname(?string $surname): self {
        $this->surname = $surname;
        return $this;
    }
}
