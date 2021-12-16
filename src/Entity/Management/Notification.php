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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[
   Document,
   ApiResource(
      collectionOperations: [
         'get'
      ],
      itemOperations: ['get'],
      paginationClientEnabled: true
   )
]
class Notification {
   #[
      ApiProperty(
         description: "Notification category"
      ),
      Length(min: 2),
      NotBlank()
      ]
    private string $category;
   #[
      ApiProperty(
         description: "Notification unique identifier",
         identifier: true
      )]
    private int $id;
   #[
      ApiProperty(
         description: "if the notification is read then `true` else set to `false`"
      )]
    private bool $read;
   #[
      ApiProperty(
         description: "Main notification content"
      ),
      Length(min: 3),
      NotBlank()
   ]
    private string $subject;

   #[ApiProperty(
      description: "Datetime when the notification has been created"
   ),
      \Symfony\Component\Validator\Constraints\DateTime()
   ]
   private DateTime $creationDatetime;

   #[ApiProperty(
      description: "Datetime when the notification has been read by the user"
   )]
   private DateTime $readDatetime;


    #[ManyToOne(Employee::class, Fetch::LAZY)]
   private Employee $user;

    public function __construct() {
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
    * @return DateTime
    */
   public function getReadDatetime(): DateTime
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
