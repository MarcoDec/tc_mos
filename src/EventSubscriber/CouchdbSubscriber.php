<?php

namespace App\EventSubscriber;

use App\Attribute\Couchdb\ORM\ManyToMany;
use App\Attribute\Couchdb\ORM\ManyToOne;
use App\Attribute\Couchdb\ORM\OneToOne;
use App\DataCollector\CouchdbLogItem;
use App\Event\Couchdb\Events;
use App\Event\Couchdb\Item\CouchdbItemPostPersistEvent;
use App\Event\Couchdb\Item\CouchdbItemPostRemoveEvent;
use App\Event\Couchdb\Item\CouchdbItemPostUpdateEvent;
use App\Event\Couchdb\Item\CouchdbItemPrePersistEvent;
use App\Event\Couchdb\Item\CouchdbItemPreRemoveEvent;
use App\Event\Couchdb\Item\CouchdbItemPreUpdateEvent;
use App\Service\CouchDBManager;
use DateTime;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CouchdbSubscriber implements EventSubscriberInterface {
    public function __construct(private LoggerInterface $logger, private CouchDBManager $manager, private EventDispatcherInterface $dispatcher) {
    }

    #[ArrayShape([
        Events::prePersist => 'string',
        Events::postPersist => 'string',
        Events::preUpdate => 'string',
        Events::preRemove => 'string'
    ])]
   public static function getSubscribedEvents() {
       return [
           Events::prePersist => 'onPrePersist',
           Events::postPersist => 'onPostPersist',
           Events::preUpdate => 'onPreUpdate',
           Events::preRemove => 'onPreRemove'
       ];
   }

    public function onPostPersist(CouchdbItemPostPersistEvent $event): void {
        $this->logger->info(__METHOD__);
    }

    public function onPrePersist(CouchdbItemPrePersistEvent $event): void {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $entity = $event->getEntity();
        $class = get_class($entity);
        $couchLog = new CouchdbLogItem($class, CouchdbLogItem::METHOD_CREATE, __METHOD__);
        $this->logger->info(__METHOD__);
        try {
            // 1. Récupération Document
            $couchdbDoc = $this->manager->documentRead($class);
            if ($couchdbDoc === null) throw new Exception("Impossible de récupérer le document CouchDB $class");
            // 2. Récupération du contenu
            $docContent = $couchdbDoc->getContent();
            // 3. Détermination du plus grand indice
            $maxId = collect($docContent)->max('id');
            $newId = $maxId + 1;
            // 4. Définition nouvel élément
            $reflectionClass = new ReflectionClass($entity);
            $properties = $reflectionClass->getProperties();
            $itemContent = [];

           $this->extractEntityData($properties, $entity,$itemContent);

           $itemContent['id'] = $newId;
            // 5. Ajout nouvel élément dans couchdbDoc
            $docContent[] = $itemContent;
            $couchdbDoc->setContent($docContent);
            // 6. Sauvegarde en base
            $this->manager->documentUpdate($couchdbDoc);
            $couchdbDoc = $this->manager->documentRead($class);
            if ($couchdbDoc===null) throw new Exception("Impossible de récupérer le document CouchDB $class");
            $couchdbItem = $couchdbDoc->getItem($newId);
            if ($couchdbItem===null) throw new Exception("Impossible de récupérer l'item $newId du document CouchDB $class");
            $entity = $couchdbItem->getEntity();
            if ($entity === null) throw new Exception("Erreur de récupération entité (retour null)");
            $newPostPersistEvent = new CouchdbItemPostPersistEvent($entity);
            $this->dispatcher->dispatch($newPostPersistEvent);
            $couchLog->setDetail("Creation nouvel item ok dans le Document $class");
            $this->manager->actions[$time] = $couchLog;
        } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->manager->actions[$time] = $couchLog;
        }
    }

    public function onPreRemove(CouchdbItemPreRemoveEvent $event): void {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $this->logger->info(__METHOD__);
        $entity = $event->getEntity();
        $class = get_class($entity);
        $couchLog = new CouchdbLogItem($class, CouchdbLogItem::METHOD_DELETE, __METHOD__);
        try {
           /** @phpstan-ignore-next-line  */
            $id = $entity->id;
            // 1. Récupération Document
            $couchdbDoc = $this->manager->documentRead($class);
            if ($couchdbDoc===null) throw new Exception("Impossible de récupérer le document CouchDB $class (null retourné)");
            // 2. Récupération du contenu
            $content = $couchdbDoc->getContent();
            // 3.Filtre du contenu
            $filteredContent = collect($content)->filter(static fn ($item) => $item['id'] != $id)->toArray();
            $couchdbDoc->setContent($filteredContent);
            // 4. Sauvegarde en base
            $this->manager->documentUpdate($couchdbDoc);
            $newPostRemoveEvent = new CouchdbItemPostRemoveEvent($entity);
            $this->dispatcher->dispatch($newPostRemoveEvent);
            $couchLog->setDetail("Suppression item $id ok dans le Document $class");
            $this->manager->actions[$time] = $couchLog;
        } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->manager->actions[$time] = $couchLog;
        }
    }

    public function onPreUpdate(CouchdbItemPreUpdateEvent $event): void {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $entity = $event->getEntity();
        $class = get_class($entity);
        $couchLog = new CouchdbLogItem($class, CouchdbLogItem::METHOD_UPDATE, __METHOD__);
        $this->logger->info(__METHOD__);
        try {
           /** @phpstan-ignore-next-line  */
            $id = $entity->id;
            // 1. Récupération Document
            $couchdbDoc = $this->manager->documentRead($class);
            if ($couchdbDoc===null) throw new Exception("Le document $class n'a pas pu être chargé depuis couchdb (null retourné)");
            // 2. Récupération du contenu
            $content = $couchdbDoc->getContent();
            // 3. Mis à jour du contenu
           $that = $this;
            $updatedContent = collect($content)->map(static function ($item) use ($id, $entity, $that) {
                if ($item['id'] === $id) {
                    $reflectionClass = new ReflectionClass($entity);
                    $properties = $reflectionClass->getProperties();
                   $that->extractEntityData($properties, $entity, $item);
                }
                return $item;
            })->toArray();
            $couchdbDoc->setContent($updatedContent);
            // 4. Sauvegarde en base
            $couchdbDoc = $this->manager->documentUpdate($couchdbDoc);
            if ($couchdbDoc===null) throw new Exception("La sauvegarde dans Couchdb d'un document à échoué");
            $updatedEntity = $couchdbDoc->getItem($id)?->getEntity();
            if ($updatedEntity===null) throw new Exception("La sauvegarde dans Couchdb du document ".$couchdbDoc->getId()." a échouée.");
            $newPostUpdateEvent = new CouchdbItemPostUpdateEvent($updatedEntity);
            $this->dispatcher->dispatch($newPostUpdateEvent);
            $couchLog->setDetail("Update item $id ok dans le Document $class");
            $this->manager->actions[$time] = $couchLog;
        } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->manager->actions[$time] = $couchLog;
        }
    }

   /**
    * @param array<ReflectionProperty> $properties
    * @param object $entity
    * @param array<string,mixed> $element
    */
   private function extractEntityData(array $properties, object $entity, array $element): void
   {
      foreach ($properties as $property) {
         $ORMManyToManyAttribute = $property->getAttributes(ManyToMany::class);
         if (count($ORMManyToManyAttribute) > 0 && ManyToMany::getPropertyData($ORMManyToManyAttribute[0])['owned']) {
            foreach ($entity->{$property->getName()} as $item) {
               $element[$property->getName()][] = $item->getId();
            }
         }
         $ORMManyToOneAttribute = $property->getAttributes(ManyToOne::class); // On persiste
         if (count($ORMManyToOneAttribute) > 0) {
            $element[$property->getName()] = $entity->{$property->getName()}->getId();
         }
         $ORMOneToOneAttribute = $property->getAttributes(OneToOne::class); // On persiste si owned === true
         if (count($ORMOneToOneAttribute) > 0 && ManyToMany::getPropertyData($ORMOneToOneAttribute[0])['owned']) {
            $element[$property->getName()] = $entity->{$property->getName()}->getId();
         }
         //On ne persiste pas les OneToMany
         //$ORMOneToManyAttribute = $property->getAttributes(OneToMany::class); //On ne persiste pas
      }
   }
}
