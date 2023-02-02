<?php

namespace App\Entity\Hr\Employee;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Hr\TimeSlot;
use App\Entity\Management\Society\Company\Company;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Équipe',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les équipes',
                    'summary' => 'Récupère les équipes',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une équipe',
                    'summary' => 'Créer une équipe',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une équipe',
                    'summary' => 'Supprime une équipe',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une équipe',
                    'summary' => 'Récupère une équipe',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une équipe',
                    'summary' => 'Modifie une équipe',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:team'],
            'openapi_definition_name' => 'Team-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:team'],
            'openapi_definition_name' => 'Team-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity
]
class Team extends Entity {
    #[
        ApiProperty(description: 'Compagnie', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:team', 'write:team'])
    ]
    private ?Company $company = null;

    /** @var Collection<int, Employee> */
    #[
        ApiProperty(description: 'Employés', readableLink: false, example: ['/api/employees/1', '/api/employees/2']),
        ORM\OneToMany(mappedBy: 'team', targetEntity: Employee::class),
        Serializer\Groups(['read:team', 'write:team'])
    ]
    private Collection $employees;

    #[
        ApiProperty(description: 'Nom', example: 'Équipe du matin'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:team', 'write:team', 'read:employee', 'read:employee:collection'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Horaires', readableLink: false, example: '/api/time-slots/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:team', 'write:team', 'read:employee', 'read:employee:collection'])
    ]
    private ?TimeSlot $timeSlot = null;

    public function __construct() {
        $this->employees = new ArrayCollection();
    }

    final public function addEmployee(Employee $employee): self {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            $employee->setTeam($this);
        }
        return $this;
    }

    final public function getCompany(): ?Company {
        return $this->company;
    }

    /**
     * @return Collection<int, Employee>
     */
    final public function getEmployees(): Collection {
        return $this->employees;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getTimeSlot(): ?TimeSlot {
        return $this->timeSlot;
    }

    final public function removeEmployee(Employee $employee): self {
        if ($this->employees->contains($employee)) {
            $this->employees->removeElement($employee);
            if ($employee->getTeam() === $this) {
                $employee->setTeam(null);
            }
        }
        return $this;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setTimeSlot(?TimeSlot $timeSlot): self {
        $this->timeSlot = $timeSlot;
        return $this;
    }
}
