<?php

namespace App\Entity\Production\Engine\Attachment;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\AbstractAttachment;
use App\Entity\Production\Engine\Manufacturer\Engine;
use App\Entity\Production\Parameter;
use App\Entity\Traits\AttachmentTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
   ORM\Entity,
   ApiResource(
      collectionOperations: [
         'get' => [
            'method' => 'GET',
            'path' => '/manufacturer-engine-attachments',
            'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
            => [
               'description' => 'Récupère la collection de fichier associé à une machine de référence',
               'summary' => 'Récupère la collection de fichier associé à une machine de référence'
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
            'path' => '/manufacturer-engine-attachments',
            'controller' => self::API_DEFAULT_UPLOAD_CONTROLLER,
            'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
            => [
               'description' => 'Créer un fichier associé à une machine de référence',
               'summary' => 'Créer un fichier associé à une machine de référence'
            ],
            'denormalization_context' => self::API_DEFAULT_DENORMALIZATION_CONTEXT,
            'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
         ]
      ],
       itemOperations: [
          'get' => [
             'openapi_context' => [
                'description' => 'Récupère un fichier associé à une machine de référence',
                'summary' => 'Récupère un fichier associé à une machine de référence'
             ],
             'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
          ],
          'delete' => [
             'openapi_context' => [
                'description' => 'Supprime un fichier associé à une machine de référence',
                'summary' => 'Supprime un fichier associé à une machine de référence'
             ]
          ]
       ],
       paginationClientEnabled: true,
       paginationItemsPerPage: 2
   ),
   ApiFilter(SearchFilter::class, properties: ['engine' => 'exact', 'category' => 'partial'])
]
class ManufacturerEngineAttachment extends AbstractAttachment
{
   use AttachmentTrait;

   #[
      ORM\Column,
      ApiProperty(description: 'Catégorie de fichier', required: true, example: 'qualité', openapiContext: [
         'enum' => ['contrats', 'doc_a_date', 'doc_a_date/capabilité', 'maintenance', 'doc', 'qualité']
      ]),
      Groups(AbstractAttachment::API_GROUPS_CATEGORY)
   ]
   private string $category = 'doc';

   #[
      ORM\ManyToOne(targetEntity: Engine::class, inversedBy: 'attachments'),
      ApiProperty(description: 'Machine de référence à laquelle doit se rattacher le fichier', required: true, example: '/api/engines/1'),
      Groups([self::API_GROUP_READ, self::API_GROUP_WRITE])
   ]
   private Engine $engine;

   public function getBaseFolder(): string {
      $path = explode('\\', Engine::class);
      return '/'.array_pop($path).'/'.$this->getEngine()->getId();
   }

   public function getExpirationDirectoriesParameter(): string
   {
      return Parameter::ENGINE_EXPIRATION_DIRECTORIES;
   }

   public function getExpirationDurationParameter(): string
   {
      return Parameter::ENGINE_EXPIRATION_DURATION;
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
    * @return Engine
    */
   public function getEngine(): Engine
   {
      return $this->engine;
   }

   /**
    * @param Engine $engine
    */
   public function setEngine(Engine $engine): void
   {
      $this->engine = $engine;
   }


}