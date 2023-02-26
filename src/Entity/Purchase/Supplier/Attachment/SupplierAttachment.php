<?php

namespace App\Entity\Purchase\Supplier\Attachment;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Purchase\Parameter;
use App\Entity\AbstractAttachment;
use App\Entity\Purchase\Supplier\Supplier;
use App\Entity\Traits\AttachmentTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
   ORM\Entity,
   ApiResource(
      collectionOperations: [
         'get' => [
            'method' => 'GET',
            'path' => '/supplier-attachments',
            'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
            => [
               'description' => 'Récupère la collection de fichier associé à un fournisseur',
               'summary' => 'Récupère la collection de fichier associé à un fournisseur'
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
            'path' => '/supplier-attachments',
            'controller' => self::API_DEFAULT_UPLOAD_CONTROLLER,
            'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
            => [
               'description' => 'Créer un fichier associé à un fournisseur',
               'summary' => 'Créer un fichier associé à un fournisseur'
            ],
            'denormalization_context' => self::API_DEFAULT_DENORMALIZATION_CONTEXT,
            'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
         ]
      ],
      itemOperations: [
         'get' => [
            'openapi_context' => [
               'description' => 'Récupère un fichier associé à un fournisseur',
               'summary' => 'Récupère un fichier associé à un fournisseur'
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
      paginationItemsPerPage: 2
   ),
   ApiFilter(SearchFilter::class, properties: ['supplier' => 'exact', 'category' => 'partial'])
]
class SupplierAttachment extends AbstractAttachment
{
   use AttachmentTrait;

   #[
      ORM\Column,
      ApiProperty(description: 'Catégorie de fichier', required: true, example: 'PIC', openapiContext: [
         'enum' => ['contrats', 'doc_a_date', 'doc', 'qualité']
      ]),
      Groups(AbstractAttachment::API_GROUPS_CATEGORY)
   ]
   private string $category = 'doc';

   #[
      ORM\ManyToOne(targetEntity: Supplier::class, inversedBy: 'attachments'),
      ApiProperty(description: 'Fournisseur auquel est rattaché le fichier', required: true, example: '/api/suppliers/1'),
      Groups([self::API_GROUP_READ, self::API_GROUP_WRITE])
   ]
   private Supplier $supplier;

   public function getBaseFolder(): string {
      $path = explode('\\', Supplier::class);
      return '/'.array_pop($path).'/'.$this->getSupplier()->getId();
   }

   public function getExpirationDirectoriesParameter(): string
   {
      return Parameter::SUPPLIER_EXPIRATION_DIRECTORIES;
   }

   public function getExpirationDurationParameter(): string
   {
      return Parameter::SUPPLIER_EXPIRATION_DURATION;
   }

   public function getExpirationDateStr(): string
   {
      return "month";
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
    * @return Supplier
    */
   public function getSupplier(): Supplier
   {
      return $this->supplier;
   }

   /**
    * @param Supplier $supplier
    */
   public function setSupplier(Supplier $supplier): void
   {
      $this->supplier = $supplier;
   }


}