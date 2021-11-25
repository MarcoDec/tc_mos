<?php

namespace App\Entity\Management;

use App\Attribute\Couchdb\Document;

#[Document]
class Notification
{
   private string $subject;
   private string $category;

   /**
    * @return string
    */
   public function getSubject(): string
   {
      return $this->subject;
   }

   /**
    * @param string $subject
    * @return Notification
    */
   public function setSubject(string $subject): self
   {
      $this->subject = $subject;
      return $this;
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
    * @return Notification
    */
   public function setCategory(string $category): self
   {
      $this->category = $category;
      return $this;
   }
   private bool $read;

   /**
    * @return bool
    */
   public function isRead(): bool
   {
      return $this->read;
   }

   /**
    * @param bool $read
    * @return Notification
    */
   public function setRead(bool $read): self
   {
      $this->read = $read;
      return $this;
   }


}