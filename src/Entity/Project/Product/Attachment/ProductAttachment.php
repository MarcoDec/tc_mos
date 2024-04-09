<?php

namespace App\Entity\Project\Product\Attachment;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\AbstractAttachment;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Project\Parameter;
use App\Entity\Project\Product\Product;
use App\Entity\Traits\AttachmentTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
   ORM\Entity,
   ApiResource(
      collectionOperations: [
         'get' => [
            'method' => 'GET',
            'path' => '/product-attachments',
            'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
            => [
               'description' => 'Récupère la collection de fichier associé à un produit',
               'summary' => 'Récupère la collection de fichier associé à un produit'
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
            'path' => '/product-attachments',
            'controller' => self::API_DEFAULT_UPLOAD_CONTROLLER,
            'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
            => [
               'description' => 'Créer un fichier associé à un produit',
               'summary' => 'Créer un fichier associé à un produit'
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
   ApiFilter(SearchFilter::class, properties: ['product' => 'exact', 'category' => 'partial'])
]
class ProductAttachment extends AbstractAttachment
{
   use AttachmentTrait;

   #[
      ORM\Column,
      ApiProperty(description: 'Catégorie de fichier', required: true, example: 'doc', openapiContext: [
         'enum' => ['doc_a_date', 'doc_a_date/audits', 'dossier_affaires', 'dossiers_fabrication', 'PPAP', 'rapports_de_controle','technique']
      ]),
      Groups(AbstractAttachment::API_GROUPS_CATEGORY)
   ]
   private string $category = 'doc';

   #[
      ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'attachments'),
      ApiProperty(description: 'Produit auquel doit se rattacher le fichier', required: true, example: '/api/products/1'),
      Groups([self::API_GROUP_READ, self::API_GROUP_WRITE])
   ]
   private Product $product;

   public function getBaseFolder(): string {
      $path = explode('\\', Product::class);
      return '/'.array_pop($path).'/'.$this->getProduct()->getId();
   }

   public function getExpirationDirectoriesParameter(): string
   {
      return Parameter::PRODUCT_EXPIRATION_DIRECTORIES;
   }

   public function getExpirationDurationParameter(): string
   {
      return Parameter::PRODUCT_EXPIRATION_DURATION;
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
    * @return Product
    */
   public function getProduct(): Product
   {
      return $this->product;
   }

   /**
    * @param Product $product
    */
   public function setProduct(Product $product): void
   {
      $this->product = $product;
   }

}
