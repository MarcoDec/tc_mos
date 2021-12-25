<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Attribute\Couchdb\Abstract\Fetch;
use App\Attribute\Couchdb\Document;
use App\Attribute\Couchdb\ORM\ManyToOne;
use App\Entity\Hr\Employee\Employee;
use DateTime;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[
   Document,
   ApiResource(
      collectionOperations: [
         'get'=>[
            'openapi_context' => [
               'description' => 'Récupère les notifications',
               'summary' => 'Récupère les notifications',
            ],
            'normalization_context' => ['groups'=>["read:category","read:id", "read:read", "read:subject", "read:creationDate", "read:readDatetime", "read:user"]]
         ],
         'post'=>[
            'openapi_context' => [
               'description' => 'Créer une notification',
               'summary' => 'Créer une notification',
            ],
            'denormalization_context' => [ 'groups' => ["write:category","write:subject", "write:user"] ],
            'normalization_context' => ['groups' => ["read:category", "read:id", "read:read", "read:subject", "read:creationDate", "read:readDatetime", "read:user"]]
         ]
      ],
      itemOperations: [
         'get'=>[
            'openapi_context' => [
               'description' => 'Récupère une notification particulière',
               'summary' => 'Récupère une notification particulière',
               ],
            'normalization_context' => [ 'groups' => ["read:category", "read:id", "read:read", "read:subject", "read:creationDate", "read:readDatetime","read:user" ]]
         ],
         'patch' =>[
            'openapi_context' => [
               'description' => 'Modifie une notification particulière',
               'summary' => 'Modifie une notification'
            ],
            'normalization_context' => ['groups'=>["read:category", "read:id","read:read", "read:subject","read:creationDate", "read:readDatetime", "read:user"]],
            'denormalization_context' => ['groups'=>["write:category","write:read","write:subject","write:readDatetime","write:user"]],
         ],
         'delete'=> [
            'openapi_context' => [
               'description'=> 'Supprime une notification',
               'summary' => 'Supprime une notification'
            ]
         ]
      ],
      paginationClientEnabled: false
   )
]
class Notification {
   #[
      ApiProperty(
         description: "Catégorie de la notification",
         example: "Livraison"
      ),
      Length(min: 2),
      NotBlank(),
      Groups([ "read:category","write:category"])
      ]
    private string $category="";
   #[
      ApiProperty(
         description: "Identifiant unique",
         identifier: true,
         example: "1"
      ),
      Groups(["read:id"])
   ]
    private int $id=0;
   #[
      ApiProperty(
         description: "Est à `Vrai` lorsque la notification a été lue, sinon à `Faux`",
         example: "`true`"
      ),
      Groups(["read:read", "write:read"])
   ]
    private bool $read=false;
   #[
      ApiProperty(
         description: "Sujet de la notification",
         example: "Livraison urgente"
      ),
      Length(min: 3),
      NotBlank(),
      Groups(["read:subject","write:subject"])
   ]
    private string $subject="";

   #[ApiProperty(
      description: "Date de création de la notification",
      example: "2021-12-24T12:03:34+00:00"
   ),
      Groups(["read:creationDate","write:creationDate"])
   ]
   private DateTime $creationDatetime;

   #[ApiProperty(
      description: "Date de lecture de la notification par l'utilisateur",
      example: "2021-12-24T14:03:34+00:00"
   ),
      Groups(["read:readDatetime", "write:readDatetime"])
   ]
   private DateTime|null $readDatetime=null;

    #[
       ManyToOne(Employee::class, Fetch::EAGER),
       Groups(["read:user", "write:user"]),
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
