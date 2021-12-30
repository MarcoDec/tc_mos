<?php

namespace App\Event\Couchdb\Item;

use App\Event\Couchdb\Events;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\EventDispatcher\Event;

class CouchdbItemPostRemoveEvent extends Event {
    public const NAME = Events::postRemove;

    public function __construct(protected object $entity) {
    }

    #[Pure]
 public function __toString(): string {
     /** @phpstan-ignore-next-line  */
     return 'Entity ('.get_class($this->entity).') ID: '.$this->entity->getId();
 }

    public function getEntity(): object {
        return $this->entity;
    }
}
