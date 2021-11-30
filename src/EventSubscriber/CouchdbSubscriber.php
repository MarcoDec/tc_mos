<?php

namespace App\EventSubscriber;

use App\Event\Couchdb\Events;
use App\Event\Couchdb\Item\CouchdbItemPostPersistEvent;
use App\Event\Couchdb\Item\CouchdbItemPostRemoveEvent;
use App\Event\Couchdb\Item\CouchdbItemPostUpdateEvent;
use App\Event\Couchdb\Item\CouchdbItemPrePersistEvent;
use App\Event\Couchdb\Item\CouchdbItemPreRemoveEvent;
use App\Event\Couchdb\Item\CouchdbItemPreUpdateEvent;
use App\Service\CouchDBManager;
use Doctrine\CouchDB\HTTP\HTTPException;
use JetBrains\PhpStorm\ArrayShape;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CouchdbSubscriber implements EventSubscriberInterface
{
   public function __construct(private LoggerInterface $logger, private CouchDBManager $manager, private EventDispatcherInterface $dispatcher){

   }

   #[ArrayShape([
      Events::prePersist => "string",
      Events::postPersist => "string",
      Events::preUpdate => "string",
      Events::preRemove => "string"
   ])]
   public static function getSubscribedEvents()
   {
      return [
         Events::prePersist => 'onPrePersist',
         Events::postPersist => 'onPostPersist',
         Events::preUpdate =>'onPreUpdate',
         Events::preRemove => 'onPreRemove'
      ];
   }

   public function onPostPersist(CouchdbItemPostPersistEvent $event)
   {
      $this->logger->info(__METHOD__);
   }

   /**
    * @throws ReflectionException
    * @throws HTTPException
    */
   public function onPrePersist(CouchdbItemPrePersistEvent $event)
   {
      $this->logger->info(__METHOD__);
      $entity =  $event->getEntity();
      $class = get_class($entity);
      // 1. Récupération Document
      $couchdbDoc = $this->manager->documentRead($class);
      // 2. Récupération du contenu
      $docContent = $couchdbDoc->getContent();
      // 3. Détermination du plus grand indice
      $maxId = collect($docContent)->max('id');
      $newId = $maxId +1;

      // 4. Définition nouvel élément
      $reflectionClass = new \ReflectionClass($entity);
      $properties = $reflectionClass->getProperties();
      $itemContent=[];
      foreach ($properties as $property) {
         if ($property->getName()!='id')
            $itemContent[$property->getName()]=$entity->{$property->getName()};
      }
      $itemContent['id']=$newId;
      // 5. Ajout nouvel élément dans couchdbDoc
      $docContent[]=$itemContent;
      $couchdbDoc->setContent($docContent);
      // 6. Sauvegarde en base
      $this->manager->documentUpdate($couchdbDoc);
      $couchdbDoc = $this->manager->documentRead($class);
      $couchdbItem = $couchdbDoc->getItem($newId);
      $entity=$couchdbItem->getEntity();
      $newPostPersistEvent = new CouchdbItemPostPersistEvent($entity);
      $this->dispatcher->dispatch($newPostPersistEvent, Events::postPersist);
   }

   /**
    * @throws ReflectionException
    * @throws HTTPException
    */
   public function onPreRemove(CouchdbItemPreRemoveEvent $event)
   {
      $this->logger->info(__METHOD__);
      $entity =  $event->getEntity();
      $class = get_class($entity);
      $id =$entity->id;
      // 1. Récupération Document
      $couchdbDoc = $this->manager->documentRead($class);
      // 2. Récupération du contenu
      $content = $couchdbDoc->getContent();
      // 3.Filtre du contenu
      $filteredContent = collect($content)->filter(function($item) use ($id,$entity){
         return $item['id'] != $id;
      })->toArray();
      $couchdbDoc->setContent($filteredContent);
      // 4. Sauvegarde en base
      $this->manager->documentUpdate($couchdbDoc);
      $newPostRemoveEvent = new CouchdbItemPostRemoveEvent($entity);
      $this->dispatcher->dispatch($newPostRemoveEvent, Events::postRemove);
   }

   /**
    * @throws HTTPException|ReflectionException
    */
   public function onPreUpdate(CouchdbItemPreUpdateEvent $event)
   {
      $this->logger->info(__METHOD__);
      $entity =  $event->getEntity();
      $class = get_class($entity);
      $id =$entity->id;
      // 1. Récupération Document
      $couchdbDoc = $this->manager->documentRead($class);
      // 2. Récupération du contenu
      $content = $couchdbDoc->getContent();
      // 3. Mis à jour du contenu
      $updatedContent = collect($content)->map(function($item) use ($id,$entity){
         if ($item['id']===$id) {
            $reflectionClass = new \ReflectionClass($entity);
            $properties = $reflectionClass->getProperties();
            foreach ($properties as $property) {
               $item[$property->getName()]=$entity->{$property->getName()};
            }
         };
         return $item;
      })->toArray();
      $couchdbDoc->setContent($updatedContent);
      // 4. Sauvegarde en base
      $couchdbDoc=$this->manager->documentUpdate($couchdbDoc);
      $updatedEntity = $couchdbDoc->getItem($id)->getEntity();
      $newPostUpdateEvent = new CouchdbItemPostUpdateEvent($updatedEntity);
      $this->dispatcher->dispatch($newPostUpdateEvent, Events::postUpdate);
   }
}