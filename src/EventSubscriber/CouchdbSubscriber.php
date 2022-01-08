<?php

namespace App\EventSubscriber;

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
use ReflectionObject;
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
            if ($couchdbDoc === null) {
                throw new Exception("Impossible de récupérer le document CouchDB $class");
            }
            // 2. Récupération du contenu
            $docContent = $couchdbDoc->getContent();
            // 3. Détermination du plus grand indice
            $maxId = collect($docContent)->max('id');
            $newId = $maxId + 1;
            // 4. Définition nouvel élément
            $itemContent = $this->manager->convertEntityToArray($entity);
            $itemContent['id'] = $newId;
            // 5. Ajout nouvel élément dans couchdbDoc
            $docContent[] = $itemContent;
            $couchdbDoc->setContent($docContent);
            // 6. Sauvegarde en base
            $this->manager->documentUpdate($couchdbDoc);
            $couchdbDoc = $this->manager->documentRead($class, true);
            if ($couchdbDoc === null) {
                throw new Exception("Impossible de récupérer le document CouchDB $class");
            }
            $couchdbItem = $couchdbDoc->getItem($newId);
            if ($couchdbItem === null) {
                throw new Exception("Impossible de récupérer l'item $newId du document CouchDB $class");
            }
            $entity = $this->manager->convertCouchdbItemToEntity($couchdbItem, $class);
            if ($entity === null) {
                throw new Exception('Erreur de récupération entité (retour null)');
            }
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
        $refEntity = new ReflectionObject($entity);
        /** @var ReflectionProperty $refIdProperty */
        $refIdProperty = collect($refEntity->getProperties())->filter(static fn (ReflectionProperty $property) => $property->getName() == 'id')->first();
        $refIdProperty->setAccessible(true);
        $class = get_class($entity);
        $couchLog = new CouchdbLogItem($class, CouchdbLogItem::METHOD_DELETE, __METHOD__);
        try {
            $id = $refIdProperty->getValue($entity);
            // 1. Récupération Document
            $couchdbDoc = $this->manager->documentRead($class);
            if ($couchdbDoc === null) {
                throw new Exception("Impossible de récupérer le document CouchDB $class (null retourné)");
            }
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
        $refEntity = new ReflectionObject($entity);
        /** @var ReflectionProperty $refIdProperty */
        $refIdProperty = collect($refEntity->getProperties())->filter(static fn (ReflectionProperty $property) => $property->getName() == 'id')->first();
        $refIdProperty->setAccessible(true);
        try {
            $id = $refIdProperty->getValue($entity);
            // 1. Récupération Document
            $couchdbDoc = $this->manager->documentRead($class);
            if ($couchdbDoc === null) {
                throw new Exception("Le document $class n'a pas pu être chargé depuis couchdb (null retourné)");
            }
            // 2. Récupération du contenu
            $content = $couchdbDoc->getContent();
            // 3. Mis à jour du contenu
            $that = $this;
            $updatedContent = collect($content)->map(static function ($item) use ($id, $entity, $that) {
                if ($item['id'] === $id) {
                    $item = $that->manager->convertEntityToArray($entity);
                }
                return $item;
            })->toArray();
            $couchdbDoc->setContent($updatedContent);
            // 4. Sauvegarde en base
            $couchdbDoc = $this->manager->documentUpdate($couchdbDoc);
            if ($couchdbDoc === null) {
                throw new Exception('La sauvegarde dans Couchdb d\'un document à échoué');
            }
            if ($couchdbItem = $couchdbDoc->getItem($id)) {
                $updatedEntity = $this->manager->convertCouchdbItemToEntity($couchdbItem, $class);
                if ($updatedEntity === null) {
                    throw new Exception('La sauvegarde dans Couchdb du document '.$couchdbDoc->getId().' a échouée.');
                }
                $newPostUpdateEvent = new CouchdbItemPostUpdateEvent($updatedEntity);
                $this->dispatcher->dispatch($newPostUpdateEvent);
                $couchLog->setDetail("Update item $id ok dans le Document $class");
                $this->manager->actions[$time] = $couchLog;
            }
        } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->manager->actions[$time] = $couchLog;
        }
    }
}
