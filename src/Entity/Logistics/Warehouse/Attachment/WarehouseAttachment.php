<?php

namespace App\Entity\Logistics\Warehouse\Attachment;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\AbstractAttachment;
use App\Entity\Logistics\Warehouse\Warehouse;
use App\Entity\Logistics\Parameter;
use App\Entity\Traits\AttachmentTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
   ORM\Entity,
   ApiResource(
       collectionOperations: [
           'get' => [
               'method' => 'GET',
               'path' => '/warehouse-attachments',
               'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
                  => [
                      'description' => 'Récupère la collection de fichier associé à un entrepôt',
                      'summary' => 'Récupère la collection de fichier associé à un entrepôt'
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
               'path' => '/warehouse-attachments',
               'controller' => self::API_DEFAULT_UPLOAD_CONTROLLER,
               'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
                  => [
                      'description' => 'Créer un fichier associé à un entrepôt',
                      'summary' => 'Créer un fichier associé à un entrepôt'
                  ],
               'denormalization_context' => self::API_DEFAULT_DENORMALIZATION_CONTEXT,
               'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
           ]
       ],
       itemOperations: [
           'get' => [
               'openapi_context' => [
                      'description' => 'Récupère un fichier associé à un entrepôt',
                      'summary' => 'Récupère un fichier associé à un entrepôt'
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
   ApiFilter(SearchFilter::class, properties: ['warehouse' => 'exact', 'category' => 'partial'])
]
class WarehouseAttachment extends AbstractAttachment {
    use AttachmentTrait;

    #[
      ORM\Column,
      ApiProperty(description: 'Catégorie de fichier', required: true, example: 'plans', openapiContext: [
          'enum' => ['plans', 'divers']
      ]),
      Groups(AbstractAttachment::API_GROUPS_CATEGORY)
   ]
   private string $category = 'doc';

    #[
      ORM\ManyToOne(targetEntity: Warehouse::class, inversedBy: 'attachments'),
      ApiProperty(description: 'Entrepôt auquel doit se rattacher le fichier', required: true, example: '/api/warehouses/1'),
      Groups([self::API_GROUP_READ, self::API_GROUP_WRITE])
   ]
   private Warehouse $warehouse;

    public function getBaseFolder(): string {
        $path = explode('\\', Warehouse::class);
        return '/'.array_pop($path).'/'.$this->getWarehouse()->getId();
    }

    public function getWarehouse(): Warehouse {
        return $this->warehouse;
    }

    public function getExpirationDirectoriesParameter(): string {
        return Parameter::WAREHOUSE_EXPIRATION_DIRECTORIES;
    }

    public function getExpirationDurationParameter(): string {
        return Parameter::WAREHOUSE_EXPIRATION_DURATION;
    }

    public function getParameterClass(): string {
        return Parameter::class;
    }

    public function setWarehouse(Warehouse $warehouse): void {
        $this->warehouse = $warehouse;
    }

   public function getExpirationDateStr(): string
   {
      return "month";
   }
}
