<?php

namespace App\Entity\Purchase\Component\Attachment;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\AbstractAttachment;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Parameter;
use App\Entity\Traits\AttachmentTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
   ORM\Entity,
   ApiResource(
      collectionOperations: [
         'get' => [
            'method' => 'GET',
            'path' => '/component-attachments',
            'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
            => [
               'description' => 'Récupère la collection de fichier associé à un composant',
               'summary' => 'Récupère la collection de fichier associé à un composant'
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
            'path' => '/component-attachments',
            'controller' => self::API_DEFAULT_UPLOAD_CONTROLLER,
            'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
            => [
               'description' => 'Créer un fichier associé à un composant',
               'summary' => 'Créer un fichier associé à un composant'
            ],
            'denormalization_context' => self::API_DEFAULT_DENORMALIZATION_CONTEXT,
            'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
         ]
      ],
      itemOperations: [
         'get' => [
            'openapi_context' => [
               'description' => 'Récupère un fichier associé à un composant',
               'summary' => 'Récupère un fichier associé à un composant'
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
   ApiFilter(SearchFilter::class, properties: ['composant' => 'exact', 'category' => 'partial'])
]
class ComponentAttachment extends AbstractAttachment
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
      ORM\ManyToOne(targetEntity: Component::class, inversedBy: 'attachments'),
      ApiProperty(description: 'Composant auquel doit se rattacher le fichier', required: true, example: '/api/components/1'),
      Groups([self::API_GROUP_READ, self::API_GROUP_WRITE])
   ]
   private Component $component;

   public function getBaseFolder(): string {
      $path = explode('\\', Component::class);
      return '/'.array_pop($path).'/'.$this->getComponent()->getId();
   }


   public function getExpirationDirectoriesParameter(): string
   {
      return Parameter::COMPONENT_EXPIRATION_DIRECTORIES;
   }

   public function getExpirationDurationParameter(): string
   {
      return Parameter::COMPONENT_EXPIRATION_DURATION;
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
    * @return void
    */
   public function setCategory(string $category): void
   {
      $this->category = $category;
   }

   /**
    * @return Component
    */
   public function getComponent(): Component
   {
      return $this->component;
   }

   /**
    * @param Component $component
    * @return ComponentAttachment
    */
   public function setComponent(Component $component): self
   {
      $this->component = $component;
      return $this;
   }

}