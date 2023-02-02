<?php


namespace App\Entity\Hr\TimeClock;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Hr\Employee\ClockingController;
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
            'normalization_context' => [
               'groups' => ['read:clocking:new'],
               'openapi_definition_name' => 'read-clocking-new',
               'skip_null_values' => false
            ],
            'denormalization_context' => [
               'groups' => ['create:clocking'],
               'openapi_definition_name' => 'clocking-create'
            ],
            'openapi_context' => [
               'description' => 'Créer un pointage',
               'summary' => 'Créer un pointage'
            ],
            'controller'=>ClockingController::class,
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
        Serializer\Groups(['read:clocking:collection','create:clocking','read:clocking:new']),
        ApiProperty(description: "Date et heure du pointage", example: "2023-01-31T00:32:35+00:00")
    ]
    private DateTime $date;

    #[
       ORM\ManyToOne(targetEntity: Employee::class, inversedBy: "clockings"),
       Serializer\Groups(['read:clocking:collection','create:clocking','read:clocking:new']),
       ApiProperty(description: "Employé ayant badgé", example: "/api/employees/1")
    ]
    private Employee $employee;

   #[
      ORM\Column(type: "boolean", nullable: false,),
      Serializer\Groups(['read:clocking:collection','read:clocking:new']),
      ApiProperty(description: "Entrée (True) ou Sortie (False)", example: "False")
   ]
    private bool $enter;

   #[
      ORM\Column(type: "datetime", nullable: true)
   ]
   private DateTime $creationDate;

   public function __construct() {
      $this->enter = false;
      $this->creationDate = new DateTime("now");
   }

   /**
    * @return DateTime
    */
   public function getCreationDate(): DateTime
   {
      return $this->creationDate;
   }

   /**
    * @param DateTime $creationDate
    */
   public function setCreationDate(DateTime $creationDate): void
   {
      $this->creationDate = $creationDate;
   }

   /**
    * @return bool
    */
   public function isEnter(): bool
   {
      return $this->enter;
   }

   /**
    * @param bool $enter
    * @return Clocking
    */
   public function setEnter(bool $enter): self
   {
      $this->enter = $enter;
      return $this;
   }



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