<?php

namespace App\Event\Couchdb;

final class Events
{

   /**
    * @Event("App\Event\Couchdb\Item\CouchdbItemOnFlushEvent")
    */
   public const onFlush='couchdb.item.onFlush';
   /**
    * @Event("App\Event\Couchdb\Item\CouchdbItemPostFlushEvent")
    */
   public const postFlush='couchdb.item.postFlush';
   /**
    * @Event("App\Event\Couchdb\Item\CouchdbItemPostLoadEvent")
    */
   public const postLoad='couchdb.item.postLoad';
   /**
    * @Event("App\Event\Couchdb\Item\CouchdbItemPostPersistEvent")
    */
   public const postPersist='couchdb.item.postPersist';
   /**
    * @Event("App\Event\Couchdb\Item\CouchdbItemPostRemoveEvent")
    */
   public const postRemove='couchdb.item.postRemove';
   /**
    * @Event("App\Event\Couchdb\Item\CouchdbItemPostUpdateEvent")
    */
   public const postUpdate='couchdb.item.postUpdate';
   /**
    * @Event("App\Event\Couchdb\Item\CouchdbItemPreFlushEvent")
    */
   public const preFlush='couchdb.item.preFlush';
   /**
    * @Event("App\Event\Couchdb\Item\CouchdbItemPreLoadEvent")
    */
   public const preLoad='couchdb.item.preLoad';
   /**
    * @Event("App\Event\Couchdb\Item\CouchdbItemPrePersistEvent")
    */
   public const prePersist='couchdb.item.prePersist';
   /**
    * @Event("App\Event\Couchdb\Item\CouchdbItemPreRemoveEvent")
    */
   public const preRemove='couchdb.item.preRemove';
   /**
    * @Event("App\Event\Couchdb\Item\CouchdbItemPreUpdateEvent")
    */
   public const preUpdate='couchdb.item.preUpdate';
}