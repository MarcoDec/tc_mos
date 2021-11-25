<?php

namespace App\Event;

use App\Entity\Entity;
use Symfony\Contracts\EventDispatcher\Event;

class CouchdbPrePersistEvent extends Event
{
   public const NAME='couchdb.prepersist';

   public function __construct(protected Entity $entity) {
   }

   public function getEntity():Entity {
      return $this->entity;
   }

   public function __toString():string {
      return 'Entity ('.get_class($this->entity).') ID: '.$this->entity->getId();
   }

}