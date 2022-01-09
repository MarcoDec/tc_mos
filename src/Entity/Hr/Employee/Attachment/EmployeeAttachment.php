<?php

namespace App\Entity\Hr\Employee\Attachment;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\AbstractAttachment;
use App\Entity\Traits\AttachmentTrait;
use Doctrine\ORM\Mapping as ORM;

#[
   ORM\Entity,
   ApiResource(
      collectionOperations: //self::API_DEFAULT_COLLECTIONS_OPERATIONS,
      [
         'upload' => [
            'input_formats'=>[
               'multipart'=>[ 'multipart/form-data' ]
            ],
            'read' => true,
            'write' => false,
            //'output' => true,
            //'input' => true,
            'deserialize'=>false,
            'method' => 'POST',
            'path' => '/employee-attachments',
            'controller' => self::API_DEFAULT_CONTROLLER,
            'openapi_context' => //self::API_DEFAULT_OPENAPI_CONTEXT,
               [
               'description' => "Créer un fichier associé à un employé",
               'summary' => "Créer un fichier associé à un employé"
               ],
            'denormalization_context' => self::API_DEFAULT_DENORMALIZATION_CONTEXT,
            'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
         ]
      ],
      itemOperations: [
         'get'=> [
            'openapi_context' =>
               [
                  'description' => "Récupère un fichier associé à un employé",
                  'summary' => "Récupère un fichier associé à un employé"
               ],
            'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
         ]
      ]
   )
]
class EmployeeAttachment extends AbstractAttachment
{
   use AttachmentTrait;

//   #[
//      ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'attachments'),
//
//   ]
//   private Employee $employee;
//   /**
//    * @return Employee
//    */
//   public function getEmployee(): Employee
//   {
//      return $this->employee;
//   }
//   /**
//    * @param Employee $employee
//    */
//   public function setEmployee(Employee $employee): void
//   {
//      $this->employee = $employee;
//   }

}