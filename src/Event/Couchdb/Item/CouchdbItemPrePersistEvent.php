<?php

namespace App\Event\Couchdb\Item;

use App\Entity\Entity;
use App\Event\Couchdb\Events;
use Symfony\Contracts\EventDispatcher\Event;

class CouchdbItemPrePersistEvent extends Event
{
   public const NAME=Events::prePersist;
   private object $entity;

   public function __construct(object  $entity) {
      $this->entity=$entity;
   }

   public function __toString():string {
      return 'Entity ('.get_class($this->entity).') ID: '.$this->entity->getId();
   }

   /**
    * @return object
    */
   public function getEntity():object
   {
      return $this->entity;
   }

   /**
    * @param object  $entity
    */
   public function setEntity(object $entity): void
   {
      $this->entity = $entity;
   }



}