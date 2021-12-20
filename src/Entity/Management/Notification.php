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
            'normalization_context' => ['groups'=>['get:Notification:collection']]
         ],
         'post'=>[
            'openapi_context' => [
               'description' => 'Ajoute/Créé une notification',
               'summary' => 'Ajoute/Créé une notification',
            ],
            'denormalization_context' => [ 'groups' => ['post:Notification:denorm'] ],
            'normalization_context' => ['groups' => ['post:Notification:norm']]
         ]
      ],
      itemOperations: [
         'get'=>[
            'openapi_context' => [
               'description' => 'Récupère une notification particulière',
               'summary' => 'Récupère une notification particulière',
               ],
            'normalization_context' => [ 'groups' => ['get:Notification:item' ]]
         ],
         'patch' =>[
            'openapi_context' => [
               'description' => 'Modifie une notification particulière',
               'summary' => 'Modifie une notification'
            ],
            'normalization_context' => ['groups'=>['patch:Notification:item:norm']],
            'denormalization_context' => ['groups'=>['patch:Notification:item:denorm']],
         ],
         'delete'
      ],
      paginationClientEnabled: false
   )
]
class Notification {
   #[
      ApiProperty(
         description: "Catégorie de la notification"
      ),
      Length(min: 2),
      NotBlank(),
      Groups(["get:Notification:collection", 'get:Notification:item', 'patch:Notification:item:norm', 'post:Notification:norm', 'post:Notification:denorm', 'patch:Notification:item:denorm'])
      ]
    private string $category="";
   #[
      ApiProperty(
         description: "Identifiant unique",
         identifier: true
      ),
      Groups(["get:Notification:collection", "post:Notification:norm", "get:Notification:item", "patch:Notification:item:norm"])
   ]
    private int $id=0;
   #[
      ApiProperty(
         description: "Est à `Vrai` lorsque la notification a été lue, sinon à `Faux`"
      ),
      Groups(["get:Notification:collection", "post:Notification:norm", "get:Notification:item", "patch:Notification:item:norm", 'patch:Notification:item:denorm'])
   ]
    private bool $read=false;
   #[
      ApiProperty(
         description: "Sujet de la notification",
      ),
      Length(min: 3),
      NotBlank(),
      Groups(["get:Notification:collection", "post:Notification:norm", "get:Notification:item", "patch:Notification:item:norm", "post:Notification:norm", "post:Notification:denorm", 'patch:Notification:item:denorm'])
   ]
    private string $subject="";

   #[ApiProperty(
      description: "Date de création de la notification"
   ),
      Groups(["get:Notification:collection", "post:Notification:norm", "get:Notification:item", "patch:Notification:item:norm"])
   ]
   private DateTime $creationDatetime;

   #[ApiProperty(
      description: "Date de lecture de la notification par l'utilisateur"
   ),
      Groups(["get:Notification:collection", "post:Notification:norm", "get:Notification:item", "patch:Notification:item:norm", "patch:Notification:item:denorm"])
   ]
   private DateTime|null $readDatetime=null;

    #[
       ManyToOne(Employee::class, Fetch::EAGER),
       Groups(["get:Notification:collection", "post:Notification:norm", "get:Notification:item", "patch:Notification:item:norm", "post:Notification:denorm", "patch:Notification:item:denorm"])
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
