<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Attribute\Couchdb\Abstract\Fetch;
use App\Attribute\Couchdb\Document;
use App\Attribute\Couchdb\ORM\ManyToOne;
use App\Controller\Management\GetNotifications;
use App\Controller\Management\PatchNotificationRead;
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
            'read'=>false,
            'write'=>false,
            'openapi_context' => [
               'description' => "Récupère les notifications liées à l'utilisateur courant",
               'summary' => "Récupère les notifications liées à l'utilisateur courant",
            ],
            'normalization_context' => ['groups'=>[
               "notification:read:category",
               "notification:read:id",
               "notification:read:read",
               "notification:read:subject",
               "notification:read:creationDate",
               "notification:read:readDatetime",
               "notification:read:targetUser"]]
         ],
         'post'=>[
            'openapi_context' => [
               'description' => 'Créer une notification',
               'summary' => 'Créer une notification',
            ],
            'denormalization_context' => [ 'groups' => [
               "notification:write:category",
               "notification:write:subject",
               "notification:write:targetUser"] ],
            'normalization_context' => ['groups' => [
               "notification:read:category",
               "notification:read:id",
               "notification:read:read",
               "notification:read:subject",
               "notification:read:creationDate",
               "notification:read:readDatetime",
               "notification:read:targetUser"]]
         ]
      ],
      itemOperations: [
         'get'=>[
            'openapi_context' => [
               'description' => 'Récupère une notification particulière',
               'summary' => 'Récupère une notification particulière',
               ],
            'normalization_context' => [ 'groups' => [
               "notification:read:category",
               "notification:read:id",
               "notification:read:read",
               "notification:read:subject",
               "notification:read:creationDate",
               "notification:read:readDatetime",
               "notification:read:targetUser" ]]
         ],
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
         'patch' =>[
            'openapi_context' => [
               'description' => 'Modifie une notification particulière',
               'summary' => 'Modifie une notification'
            ],
            'normalization_context' => ['groups'=>[
               "notification:read:category",
               "notification:read:id",
               "notification:read:read",
               "notification:read:subject",
               "notification:read:creationDate",
               "notification:read:readDatetime",
               "notification:read:targetUser"]],
            'denormalization_context' => ['groups'=>[
               "notification:write:category",
               "notification:write:read",
               "notification:write:subject",
               "notification:write:readDatetime",
               "notification:write:targetUser"]],
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
         "notification:read:category",
         "notification:write:category"])
      ]
    private string $category="";
   #[
      ApiProperty(
         description: "Identifiant unique",
         identifier: true,
         example: "1"
      ),
      Groups(["notification:read:id"])
   ]
    private int $id=0;
   #[
      ApiProperty(
         description: "Est à `Vrai` lorsque la notification a été lue, sinon à `Faux`",
         example: "`true`"
      ),
      Groups([
         "notification:read:read",
         "notification:write:read"])
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
         "notification:read:subject",
         "notification:write:subject"])
   ]
    private string $subject="";

   #[ApiProperty(
      description: "Date de création de la notification",
      example: "2021-12-24T12:03:34+00:00"
   ),
      Groups([
         "notification:read:creationDate",
         "notification:write:creationDate"])
   ]
   private DateTime $creationDatetime;

   #[ApiProperty(
      description: "Date de lecture de la notification par l'utilisateur",
      example: "2021-12-24T14:03:34+00:00"
   ),
      Groups([
         "notification:read:readDatetime",
         "notification:write:readDatetime"])
   ]
   private DateTime|null $readDatetime=null;

    #[
       ManyToOne(Employee::class, Fetch::EAGER),
       Groups([
          "notification:read:targetUser",
          "notification:write:targetUser"]),
       ApiProperty(
          description: "Employé destinataire de la notification",
          example: "/api/users/1"
       )
    ]
   private Employee $user;

    public function __construct() {
       $this->creationDatetime = new DateTime('now');
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
    * @return Employee
    */
    public function getUser(): Employee {
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
    * @param Employee $user
    */
    public function setUser(Employee $user): void {
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
