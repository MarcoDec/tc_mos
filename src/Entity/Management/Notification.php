<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Attribute\Couchdb\Abstract\Fetch;
use App\Attribute\Couchdb\Document;
use App\Attribute\Couchdb\ORM\ManyToOne;
use App\Controller\Management\DeleteNotificationCategoryAll;
use App\Controller\Management\GetNotifications;
use App\Controller\Management\PatchNotificationCategoryReadAll;
use App\Controller\Management\PatchNotificationRead;
use App\Controller\Management\PostNotifications;
use App\Entity\Hr\Employee\Employee;
use DateTime;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;

#[
   Document,
   ApiResource(
      collectionOperations: [
         'get_notifications'=>[
            'method'=>"GET",
            'path'=>"/notifications",
            'controller'=> GetNotifications::class,
            'read'=>true,
            'write'=>true,
            'openapi_context' => [
               'description' => "Récupère les notifications non lues liées à l'utilisateur courant",
               'summary' => "Récupère les notifications non lues liées à l'utilisateur courant",
            ],
            'normalization_context' => ['groups'=>["notification:read"]]
         ],
         'post'=>[
            'method'=>"POST",
            'path'=>'/notifications',
            'controller'=> PostNotifications::class,
            'read'=>false,
            'openapi_context' => [
               'description' => 'Créer une notification',
               'summary' => 'Créer une notification',
            ],
            'denormalization_context' => [ 'groups' => ["notification:create"] ],
            'normalization_context' => ['groups' => ["notification:read"]]
         ],
         'patch_notification_category_read_all' =>[
            'method'=> 'PATCH',
            'path'=> '/notifications/{category}/read-all',
            'requirements' => ['category' => '(\w\s?)+'],
            'controller'=>PatchNotificationCategoryReadAll::class,
            'read'=>false,
            'write'=>false,
            'input'=>false,
            'openapi_context' => [
               'parameters'=>[[
                  'in'=>'path',
                  'name'=>'category',
                  'type'=>'string',
                  'required'=>true
               ]],
               'description' => "Marque les notifications de l'utilisateur appartenant à la catégorie comme lues",
               'summary' => "Marque les notifications de l'utilisateur appartenant à la catégorie comme lues",
               'requestBody' => [
                  'required' =>false,
                  'content' => [
                     'application/ld+json'=> [
                        'schema'=>[],
                        'example'=>'{}'
                     ]
                  ]
               ],

            ],
            'normalization_context' => ['groups'=>["notification:read"]],
            'denormalization_context' => ['groups'=>["notification:write"]],
         ],
         'delete_notification_category_all' =>[
            'method'=> 'DELETE',
            'path'=> '/notifications/{category}/all',
            'requirements' => ['category' => '(\w\s?)+'],
            'controller'=>DeleteNotificationCategoryAll::class,
            'read'=>false,
            'write'=>false,
            'input'=>false,
            'openapi_context' => [
               'parameters'=>[[
                  'in'=>'path',
                  'name'=>'category',
                  'type'=>'string',
                  'required'=>true
               ]],
               'description' => "Supprime les notifications de l'utilisateur appartenant à la catégorie",
               'summary' => "Supprime les notifications de l'utilisateur appartenant à la catégorie",
               'requestBody' => [
                  'required' =>false,
                  'content' => [
                     'application/ld+json'=> [
                        'schema'=>[],
                        'example'=>'{}'
                     ]
                  ]
               ],

            ],
            'normalization_context' => ['groups'=>["notification:read"]],
            'denormalization_context' => ['groups'=>["notification:write"]],
         ],
      ],
      itemOperations: [
         'get'=>NO_ITEM_GET_OPERATION,
         'patch_notification_read'=>[
            'method' => 'PATCH',
            'path' => '/notifications/{id}/read',
            'controller' => PatchNotificationRead::class,
            'read'=>false,
            'write'=>false,
            'output'=>false,
            'input'=>false,
            'openapi_context' => [
               'description' => 'Marque la notification comme lue',
               'summary' => 'Marque la notification comme lue',
               'requestBody' => [
                  'required' => false,
                  'content'=>[
                     'application/ld+json'=>[
                        'schema'=> [],
                        'example'=>'{}'
                     ]
                  ]
               ]
            ]
         ],
         'delete'=> [
            'openapi_context' => [
               'description'=> 'Supprime une notification',
               'summary' => 'Supprime une notification'
            ]
         ]
      ],
      shortName: 'Notification',
      paginationEnabled: false
   )
]
class Notification {
   #[
      ApiProperty(
         description: "Catégorie de la notification",
         example: "Livraison",
      ),
      Length(min: 2),
      NotBlank(),
      Groups([
         "notification:read",
         "notification:write", "notification:create"])
      ]
    private string $category="";
   #[
      ApiProperty(
         description: "Identifiant unique",
         identifier: true,
         example: "1"
      ),
      Groups(["notification:read"])
   ]
    private int $id=0;
   #[
      ApiProperty(
         description: "Est à `Vrai` lorsque la notification a été lue, sinon à `Faux`",
         example: "`true`"
      ),
      Groups([
         "notification:read",
         "notification:write"])
   ]
    private bool $read=false;
   #[
      ApiProperty(
         description: "Sujet de la notification",
         example: "Livraison urgente"
      ),
      Length(min: 3),
      NotBlank(),
      Groups([
         "notification:read",
         "notification:write", "notification:create"])
   ]
    private string $subject="";

   #[ApiProperty(
      description: "Date de création de la notification",
      example: "2021-12-24T12:03:34+00:00"
   ),
      Groups([
         "notification:read",
         "notification:write"])
   ]
   private DateTime $creationDatetime;

   #[ApiProperty(
      description: "Date de lecture de la notification par l'utilisateur",
      example: "2021-12-24T14:03:34+00:00"
   ),
      Groups([
         "notification:read",
         "notification:write"])
   ]
   private DateTime|null $readDatetime=null;

    #[
       ManyToOne(Employee::class, Fetch::EAGER),
       Groups([
          "notification:read",
          "notification:write"]),
       ApiProperty(
          description: "Employé destinataire de la notification",
          example: "/api/employee/username=johnDo;id=10"
       )
    ]
   private ?Employee $user=null;

    public function __construct() {
       $this->creationDatetime = new DateTime('now');
       $this->setUser(null);
    }

    #[Pure]
    public function __toString(): string {
        return $this->getId().' - '.$this->getCategory().': '.$this->getSubject();
    }

   /**
    * @return string
    */
    public function getCategory(): string {
        return $this->category;
    }

   /**
    * @return int
    */
    public function getId(): int {
        return $this->id;
    }

   /**
    * @return string
    */
    public function getSubject(): string {
        return $this->subject;
    }

   /**
    * @return Employee|null
    */
    public function getUser(): ?Employee {
        return $this->user;
    }

   /**
    * @return bool
    */
    public function isRead(): bool {
        return $this->read;
    }

   /**
    * @param string $category
    * @return Notification
    */
    public function setCategory(string $category): self {
        $this->category = $category;
        return $this;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

   /**
    * @param bool $read
    * @return Notification
    */
    public function setRead(bool $read): self {
        $this->read = $read;
        return $this;
    }

   /**
    * @param string $subject
    * @return Notification
    */
    public function setSubject(string $subject): self {
        $this->subject = $subject;
        return $this;
    }

   /**
    * @param Employee|null $user
    */
    public function setUser(?Employee $user): void {
        $this->user = $user;
    }

   /**
    * @return DateTime
    */
   public function getCreationDatetime(): DateTime
   {
      return $this->creationDatetime;
   }

   /**
    * @param DateTime $creationDatetime
    */
   public function setCreationDatetime(DateTime $creationDatetime): void
   {
      $this->creationDatetime = $creationDatetime;
   }

   /**
    * @return DateTime|null
    */
   public function getReadDatetime(): ?DateTime
   {
      return $this->readDatetime;
   }

   /**
    * @param DateTime $readDatetime
    */
   public function setReadDatetime(DateTime $readDatetime): void
   {
      $this->readDatetime = $readDatetime;
   }


}
