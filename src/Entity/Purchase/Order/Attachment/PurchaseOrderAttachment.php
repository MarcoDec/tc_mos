<?php

namespace App\Entity\Purchase\Order\Attachment;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\AbstractAttachment;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Purchase\Order\Order;
use App\Entity\Traits\AttachmentTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
   ORM\Entity,
   ApiResource(
      description: 'PurchaseOrder DeliveryForm',
      collectionOperations: [
         'get' => [
            'method' => 'GET',
            'path' => '/purchase-order-deliveryForms',
            'openapi_context' //self::API_DEFAULT_OPENAPI_CONTEXT,
            => [
               'description' => 'Récupère les bordereaux de livraison fournisseur',
               'summary' => 'Récupère les bordereaux de livraison fournisseur'
            ],
            'denormalization_context' => self::API_DEFAULT_DENORMALIZATION_CONTEXT,
            'normalization_context' => [
               'groups' => ['read:purchase-order-deliveryForms:collection', self::API_GROUP_READ],
               'openapi_definition_name' => 'purchase-order-deliveryForms-collection',
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
            'path' => '/purchase-order-deliveryForms',
            'controller' => self::API_DEFAULT_UPLOAD_CONTROLLER,
            'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')',
            'openapi_context' => [
               'description' => 'Enregistre un bordereau de livraison fournisseur',
               'summary' => 'Enregistre un bordereau de livraison fournisseur'
            ],
            'denormalization_context' => [
               'groups' => ['create:purchase-order-deliveryForms', self::API_GROUP_WRITE],
               'openapi_definition_name' => 'purchase-order-deliveryForms-create'
            ],
            'normalization_context' => [
               'groups' => ['read:purchase-order-deliveryForms:new', self::API_GROUP_READ],
               'openapi_definition_name' => 'read-purchase-order-deliveryForms-new',
               'skip_null_values' => false
            ]
         ]
      ],
      itemOperations: [
         'get' => [
            'openapi_context' => [
               'description' => 'Récupère un bordereau de livraison fournisseur',
               'summary' => 'Récupère un bordereau de livraison fournisseur'
            ],
            'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
         ],
         'delete' => [
            'openapi_context' => [
               'description' => 'Supprime un bordereau de livraison fournisseur',
               'summary' => 'Supprime un bordereau de livraison fournisseur'
            ]
         ]
      ],
      attributes: [
         'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
      ],
      paginationClientEnabled: true,
      paginationItemsPerPage: 2
   ),
   ApiFilter(SearchFilter::class, properties: ['order' => 'exact', 'category' => 'partial'])
]
class PurchaseOrderAttachment extends AbstractAttachment
{
   use AttachmentTrait;

   public function __construct() {
      parent::__construct();
      $this->hasParameter = false; // PurchaseOrderAttachment n'a pas (besoin) de paramètres
      $this->setExpirationDate(new DateTime("now + 24 month")); // On positionne à 14 jours par défaut la durée de rétention du fichier
   }

   #[
      ORM\ManyToOne(targetEntity: Order::class, inversedBy: "deliveryForms"),
      Serializer\Groups(['read:purchase-order-deliveryForms:collection','create:purchase-order-deliveryForms','read:purchase-order-deliveryForms:new']),
      ApiProperty(description: "Commande fournisseur associée", example: "/api/purchase-orders/1")
   ]
   private Order $order;

   public function getBaseFolder(): string {
      return '/purchase-orders/'.$this->getOrder()->getId();
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
      return 'month';
   }

   public function getParameterClass(): string
   {
      return '';
   }

   /**
    * @return Order
    */
   public function getOrder(): Order
   {
      return $this->order;
   }

   /**
    * @param Order $order
    */
   public function setOrder(Order $order): void
   {
      $this->order = $order;
   }


}