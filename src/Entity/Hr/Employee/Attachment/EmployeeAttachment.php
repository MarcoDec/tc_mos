<?php

namespace App\Entity\Hr\Employee\Attachment;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\AbstractAttachment;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Hr\Parameter;
use App\Entity\Traits\AttachmentTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
   ORM\Entity,
   ApiResource(
       collectionOperations: [
           'get' => [
               'method' => 'GET',
               'path' => '/employee-attachments',
               'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
                  => [
                      'description' => 'Récupère la collection de fichier associé à un employé',
                      'summary' => 'Récupère la collection de fichier associé à un employé'
                  ],
               'denormalization_context' => self::API_DEFAULT_DENORMALIZATION_CONTEXT,
               'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
           ],
           'upload' => [
               'input_formats' => [
                   'multipart' => ['multipart/form-data']
               ],
               'read' => true,
               'write' => false,
               'deserialize' => false,
               'method' => 'POST',
               'path' => '/employee-attachments',
               'controller' => self::API_DEFAULT_UPLOAD_CONTROLLER,
               'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
                  => [
                      'description' => 'Créer un fichier associé à un employé',
                      'summary' => 'Créer un fichier associé à un employé'
                  ],
               'denormalization_context' => self::API_DEFAULT_DENORMALIZATION_CONTEXT,
               'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
           ]
       ],
       itemOperations: [
           'get' => [
               'openapi_context' => [
                      'description' => 'Récupère un fichier associé à un employé',
                      'summary' => 'Récupère un fichier associé à un employé'
                  ],
               'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
           ],
           'delete' => [
               'openapi_context' => [
                   'description' => 'Supprime un fichier',
                   'summary' => 'Supprime un fichier'
               ]
           ]
       ],
       paginationClientEnabled: true,
       paginationItemsPerPage: 2
   ),
   ApiFilter(SearchFilter::class, properties: ['employee' => 'exact', 'category' => 'partial'])
]
class EmployeeAttachment extends AbstractAttachment {
    use AttachmentTrait;

    #[
      ORM\Column(type: 'string', length: 255, options: ['default' => 'doc']),
      ApiProperty(description: 'Catégorie de fichier', required: true, example: 'PIC', openapiContext: [
          'enum' => ['contrats', 'doc_a_date', 'doc_a_date/formations', 'doc', 'qualité']
      ]),
      Groups(AbstractAttachment::API_GROUPS_CATEGORY)
   ]
   private string $category = 'doc';

    #[
      ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'attachments'),
      ApiProperty(description: 'Employée auquel doit se rattacher le fichier', required: true, example: '/api/employees/1'),
      Groups([self::API_GROUP_READ, self::API_GROUP_WRITE])
   ]
   private Employee $employee;

    public function getBaseFolder(): string {
        $path = explode('\\', Employee::class);
        return '/'.array_pop($path).'/'.$this->getEmployee()->getId();
    }

    public function getEmployee(): Employee {
        return $this->employee;
    }

    public function getExpirationDirectoriesParameter(): string {
        return Parameter::EMPLOYEE_EXPIRATION_DIRECTORIES;
    }

    public function getExpirationDurationParameter(): string {
        return Parameter::EMPLOYEE_EXPIRATION_DURATION;
    }

    public function getParameterClass(): string {
        return Parameter::class;
    }

    public function setEmployee(Employee $employee): void {
        $this->employee = $employee;
    }

   public function getExpirationDateStr(): string
   {
      return "month";
   }
}
