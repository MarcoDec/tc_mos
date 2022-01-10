<?php

namespace App\Entity\Hr\Employee;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Traits\NameTrait;
use App\Filter\RelationFilter;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'employee' => 'name',
    ]),
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
            'groups' => ['write:employee-contact', 'write:name', 'write:employee'],
            'openapi_definition_name' => 'EmployeeContact-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:employee-contact', 'read:employee'],
            'openapi_definition_name' => 'EmployeeContact-read'
        ],
    ),
    ORM\Entity,
    ORM\Table(name: 'employee_contact')
]
class Contact extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Employé', required: false, example: '/api/employees/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Employee::class),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    protected ?Employee $employee;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'De Gaule'),
        Assert\NotBlank,
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Prénom', required: false, example: 'Charles'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:employee-contact', 'write:employee-contact'])
    ]
    private ?string $firstname = null;

    #[
        ApiProperty(description: 'Numéro de téléphone', required: false, example: '03 84 91 99 84'),
        AppAssert\PhoneNumber,
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:employee-contact', 'write:employee-contact'])
    ]
    private ?string $phone = null;

    final public function getEmployee(): ?Employee {
        return $this->employee;
    }

    final public function getFirstname(): ?string {
        return $this->firstname;
    }

    final public function getPhone(): ?string {
        return $this->phone;
    }

    final public function setEmployee(?Employee $employee): self {
        $this->employee = $employee;

        return $this;
    }

    final public function setFirstname(?string $firstname): self {
        $this->firstname = $firstname;

        return $this;
    }

    final public function setPhone(?string $phone): self {
        $this->phone = $phone;

        return $this;
    }
}
