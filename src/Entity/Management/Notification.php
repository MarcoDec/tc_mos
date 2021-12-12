<?php

namespace App\Entity\Management;

use App\Attribute\Couchdb\Abstract\Fetch;
use App\Attribute\Couchdb\Document;
use App\Attribute\Couchdb\ORM\ManyToOne;
use App\Entity\Hr\Employee\Employee;
use JetBrains\PhpStorm\Pure;

#[Document]
class Notification {
    public string $category;
    public int $id;
    public bool $read;
    public string $subject;

    #[ManyToOne(Employee::class, Fetch::LAZY)]
   public Employee $user;

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
}
