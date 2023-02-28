<?php


namespace App\Entity\Hr\TimeClock;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\Hr\Employee\ClockingController;
use App\Entity\AbstractAttachment;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Hr\Parameter;
use App\Entity\Traits\AttachmentTrait;
use App\Repository\Hr\TimeClock\ClockingRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
   ORM\Entity,
   ApiResource(
      description: 'Pointages',
      collectionOperations: [
         'get' => [
            'method' => 'GET',
            'path' => '/clockings',
            'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
            => [
               'description' => 'Récupère les pointages',
               'summary' => 'Récupère les pointages'
            ],
            'denormalization_context' => self::API_DEFAULT_DENORMALIZATION_CONTEXT,
            'normalization_context' => [
               'groups' => ['read:clocking:collection', self::API_GROUP_READ],
               'openapi_definition_name' => 'Clocking-collection',
               'skip_null_values' => false
            ]
         ],
         'post' => [
            'input_formats' => [
               'multipart' => ['multipart/form-data']
            ],
            'read' => true,
            'write' => false,
            'deserialize' => false,
            'method' => 'POST',
            'path' => '/clockings',
            'controller' => ClockingController::class,
            'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')',
            'openapi_context' => [
               'description' => 'Créer un pointage',
               'summary' => 'Créer un pointage'
            ],
            'denormalization_context' => [
               'groups' => ['create:clocking', self::API_GROUP_WRITE],
               'openapi_definition_name' => 'clocking-create'
            ],
            'normalization_context' => [
               'groups' => ['read:clocking:new', self::API_GROUP_READ],
               'openapi_definition_name' => 'read-clocking-new',
               'skip_null_values' => false
            ]
         ]
      ],
      itemOperations: [
         'get' => [
            'openapi_context' => [
               'description' => 'Récupère un pointage employé',
               'summary' => 'Récupère un pointage employé'
            ],
            'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
         ],
         'delete' => [
            'openapi_context' => [
               'description' => 'Supprime un pointage employé',
               'summary' => 'Supprime un pointage employé'
            ]
         ]
      ],
      attributes: [
         'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')'
      ],
      paginationItemsPerPage: 2
   ),
   ApiFilter(SearchFilter::class, properties: ['employee' => 'exact'])
]
class Clocking extends AbstractAttachment {
   use AttachmentTrait;
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
      parent::__construct();
      $this->hasParameter = false; // Clocking n'a pas (besoin) de paramètres
      $this->enter = false;
      $this->creationDate = new DateTime("now");
      $this->setExpirationDate(new DateTime("now + 14 day")); // On positionne à 14 jours par défaut la durée de rétention du fichier
   }

   public function getBaseFolder(): string {
      $path = explode('\\', Employee::class);
      return '/'.array_pop($path).'/'.$this->getEmployee()->getId();
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

   public function getExpirationDirectoriesParameter(): string
   {
      return '';
   }

   public function getExpirationDurationParameter(): string
   {
      return '';
   }

   public function getExpirationDateStr(): string
   {
      return 'day';
   }

   public function getParameterClass(): string
   {
      return '';
   }
}