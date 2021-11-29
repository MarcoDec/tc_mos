<?php

namespace App\Event\Couchdb\Item;

use App\Entity\Entity;
use App\Event\Couchdb\Events;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\EventDispatcher\Event;

class CouchdbItemPreFlushEvent extends Event
{
   public const NAME=Events::preFlush;

   public function __construct(protected object $entity) {
   }

   public function getEntity():object {
      return $this->entity;
   }

   #[Pure] public function __toString():string {
      return 'Entity ('.get_class($this->entity).') ID: '.$this->entity->getId();
   }

}