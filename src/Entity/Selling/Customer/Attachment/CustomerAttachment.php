<?php

namespace App\Entity\Selling\Customer\Attachment;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\AbstractAttachment;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Traits\AttachmentTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\Selling\Parameter;

#[
   ORM\Entity,
   ApiResource(
      collectionOperations: [
         'get' => [
            'method' => 'GET',
            'path' => '/customer-attachments',
            'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
            => [
               'description' => 'Récupère la collection de fichier associé à un client',
               'summary' => 'Récupère la collection de fichier associé à un client'
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
            'path' => '/customer-attachments',
            'controller' => self::API_DEFAULT_UPLOAD_CONTROLLER,
            'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
            => [
               'description' => 'Créer un fichier associé à un client',
               'summary' => 'Créer un fichier associé à un client'
            ],
            'denormalization_context' => self::API_DEFAULT_DENORMALIZATION_CONTEXT,
            'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
         ]
      ],
      itemOperations: [
         'get' => [
            'openapi_context' => [
               'description' => 'Récupère un fichier associé à un client',
               'summary' => 'Récupère un fichier associé à un client'
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
      paginationItemsPerPage: 2,
      paginationClientEnabled: true
   ),
   ApiFilter(SearchFilter::class, properties: ['customer' => 'exact', 'category' => 'partial'])
]
class CustomerAttachment extends AbstractAttachment
{
   use AttachmentTrait;

   #[
      ORM\Column,
      ApiProperty(description: 'Catégorie de fichier', required: true, example: 'doc', openapiContext: [
         'enum' => ['contrats', 'doc_a_date', 'doc_a_date/formations', 'doc', 'qualité']
      ]),
      Groups(AbstractAttachment::API_GROUPS_CATEGORY)
   ]
   private string $category = 'doc';

   #[
      ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'attachments'),
      ApiProperty(description: 'Client auquel doit se rattacher le fichier', required: true, example: '/api/customers/1'),
      Groups([self::API_GROUP_READ, self::API_GROUP_WRITE])
   ]
   private Customer $customer;

   public function getBaseFolder(): string {
      $path = explode('\\', Customer::class);
      return '/'.array_pop($path).'/'.$this->getCustomer()->getId();
   }

   public function getExpirationDirectoriesParameter(): string
   {
      return Parameter::CUSTOMER_EXPIRATION_DIRECTORIES;
   }

   public function getExpirationDurationParameter(): string
   {
      return Parameter::CUSTOMER_EXPIRATION_DURATION;
   }

   public function getExpirationDateStr(): string
   {
      return 'month';
   }

   public function getParameterClass(): string
   {
      return Parameter::class;
   }

   /**
    * @return string
    */
   public function getCategory(): string
   {
      return $this->category;
   }

   /**
    * @param string $category
    */
   public function setCategory(string $category): void
   {
      $this->category = $category;
   }

   /**
    * @return Customer
    */
   public function getCustomer(): Customer
   {
      return $this->customer;
   }

   /**
    * @param Customer $customer
    */
   public function setCustomer(Customer $customer): void
   {
      $this->customer = $customer;
   }


}