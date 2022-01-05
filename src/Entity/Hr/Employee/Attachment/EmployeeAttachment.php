<?php

namespace App\Entity\Hr\Employee\Attachment;

use App\Entity\AbstractAttachment;
use App\Entity\Hr\Employee\Employee;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class EmployeeAttachment extends AbstractAttachment
{
   #[ ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'attachments') ]
   private Employee $employee;
   /**
    * @return Employee
    */
   public function getEmployee(): Employee
   {
      return $this->employee;
   }
   /**
    * @param Employee $employee
    */
   public function setEmployee(Employee $employee): void
   {
      $this->employee = $employee;
   }

}