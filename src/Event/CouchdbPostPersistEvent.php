<?php

namespace App\Event;

use App\Entity\Entity;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\EventDispatcher\Event;

class CouchdbPostPersistEvent extends Event
{
   public const NAME='couchdb.postpersist';

   public function __construct(protected Entity $entity) {
   }

   public function getEntity():Entity {
      return $this->entity;
   }

   #[Pure] public function __toString():string {
      return 'Entity ('.get_class($this->entity).') ID: '.$this->entity->getId();
   }

}