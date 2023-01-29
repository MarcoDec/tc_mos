<?php


namespace App\Entity\Hr\TimeClock;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Hr\Employee\Employee;
use App\Repository\Hr\TimeClock\ClockingRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
   ApiResource(description: 'Pointages',
      collectionOperations: [
         'get' => [
            'normalization_context' => [
               'groups' => ['read:clocking:collection'],
               'openapi_definition_name' => 'Clocking-collection',
               'skip_null_values' => false
            ],
            'openapi_context' => [
               'description' => 'Récupère les pointages',
               'summary' => 'Récupère les Pointages'
            ]
         ],
         'post' => [
            'denormalization_context' => [
               'groups' => ['create:clocking'],
               'openapi_definition_name' => 'clocking-create'
            ],
            'openapi_context' => [
               'description' => 'Créer un pointage',
               'summary' => 'Créer un pointage'
            ],
            'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')'
         ]
      ],
      attributes: [
         'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')'
      ]
   ),
   ORM\Entity(repositoryClass: ClockingRepository::class)
]
class Clocking extends Entity {
    #[
        ORM\Column(type: "datetime", nullable: true),
        Serializer\Groups(['read:clocking:collection','clocking-create']),
        ApiProperty(description: "Date et heure du pointage", example: "2023-01-31T00:32:35+00:00")
    ]
    private DateTime $date;

    #[
       ORM\ManyToOne(targetEntity: Employee::class, inversedBy: "clockings"),
       Serializer\Groups(['read:clocking:collection','clocking-create']),
       ApiProperty(description: "Employé ayant badgé", example: "/api/employees/1")
    ]
    private Employee $employee;

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return Clocking
     */
    public function setDate(DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee $employee
     * @return Clocking
     */
    public function setEmployee(Employee $employee): self
    {
        $this->employee = $employee;
        return $this;
    }
}