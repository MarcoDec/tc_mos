<?php

namespace App\Service;

use App\Attribute\Couchdb\Document;
use App\Entity\Couchdb\CouchdbDocument;
use App\Entity\Couchdb\CouchdbItem;
use App\Event\Couchdb\Events;
use App\Event\Couchdb\Item\CouchdbItemPrePersistEvent;
use App\Event\Couchdb\Item\CouchdbItemPreUpdateEvent;
use Doctrine\CouchDB\CouchDBClient;
use Doctrine\CouchDB\HTTP\HTTPException;
use Doctrine\CouchDB\HTTP\Response;
use Exception;
use HaydenPierce\ClassFinder\ClassFinder;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CouchDBManager
{
   private CouchDBClient $client;

   public function __construct(private string $couchUrl, private string $couchDBName, private EventDispatcherInterface $eventDispatcher, private LoggerInterface $logger) {
      $this->init();
   }

   private function init() {
      $this->client = CouchDBClient::create([
         'url'=> $this->couchUrl,
         'dbname'=> $this->couchDBName
      ]);
   }

   /**
    * @throws HTTPException
    */
   public function createDatabase():void {
      $this->client->createDatabase($this->client->getDatabase());
   }
   public function allDocs(): Response {
      return $this->client->allDocs();
   }

   /**
    * @return array
    * @throws Exception
    * Retourne la liste des noms(id) des documents
    */
   public function getDocList():array {
      try {
         $rows =$this->allDocs()->body["rows"];
         return collect($rows)->map(function($doc){ return $doc["id"]; })->toArray();
      } catch (Exception $e) {
         throw new Exception("La base ".$this->couchDBName." n'existe pas",0);
      }
   }

   /**
    * @return array
    * @throws Exception
    * Retourne la liste des Entités qui ont l'attribut Couchdb\Document
    */
   public function getCouchdbDocuments():array {
      $allClasses = ClassFinder::getClassesInNamespace('App\Entity',ClassFinder::RECURSIVE_MODE);
      $filteredClass = collect($allClasses)->filter(function($class){
         $reflexionClass=new \ReflectionClass($class);
         return count($reflexionClass->getAttributes(Document::class))>0;
      })->toArray();
      return $filteredClass;
   }

   /**
    * @param object $entity
    * @return bool
    * Retourne Vraie si l'entité est d'une classe comportant l'attribut Couchdb/Document
    */
   public function isCouchdbDocument(object $entity):bool {
      $reflexionClass=new \ReflectionClass(get_class($entity));
      return count($reflexionClass->getAttributes(Document::class))>0;
   }

   public function documentGetRev($dbDoc):string {
      foreach ($this->allDocs()->body['rows'] as $doc) {
         if ($doc["id"]==$dbDoc) {
            return $doc["value"]["rev"];
         }
      }
      return "";
   }

   //region CRUD document
      /**
       * Créer un nouveau document
       * @throws HTTPException
       */
      public function documentCreate(array $content): CouchdbDocument
      {
         $id_rev =$this->client->postDocument($content);
         return $this->documentRead($id_rev[0]);
      }
      /**
       * Supprime un document
       * @throws HTTPException
       */
      public function documentDelete($id, $rev) {
         $this->client->deleteDocument($id,$rev);
      }
      /**
       * Charge un document existant
       * @param $id
       * @return CouchdbDocument
       */
      public function documentRead($id): CouchdbDocument {
         return new CouchdbDocument($this->client->findDocument($id));
      }

   /**
    * Met à jour un document, cela incrémente sa révision
    * @param CouchdbDocument $couchdbDocument
    * @return CouchdbDocument [$id,$rev]
    * @throws HTTPException
    */
      public function documentUpdate(CouchdbDocument $couchdbDocument): CouchdbDocument {
         $this->logger->info(__METHOD__);
         list($id, $rev) = $this->client->putDocument(['content'=>$couchdbDocument->getContent()], $couchdbDocument->getId(), $couchdbDocument->getRev());
         return $this->documentRead($id);
      }
   //endregion
   //region CRUD item
   /**
    * Crée un item dans la base
    * @param  $entity
    * @return object
    */
      public function itemCreate($entity):object {
         $this->logger->info(__CLASS__.'/'.__METHOD__);
         $newEventPrePersist = new CouchdbItemPrePersistEvent($entity);
         $this->eventDispatcher->dispatch($newEventPrePersist,Events::prePersist);
         return $newEventPrePersist->getEntity();
      }

      /**
       * Supprime un item dans la base
       * @param object $entity
       * @throws HTTPException
       */
      public function itemDelete(object $entity):void {
            $id =$entity->getId();
            $class = get_class($entity);
            // 1. Récupération Document
            $couchdbDoc = $this->documentRead($class);
            // 2. Récupération du contenu
            $content = $couchdbDoc->getContent();
            // 3. Filtre du contenu
            $newContent = collect($content)->filter(function($item) use ($id){
               return $item['id']!=$id;
            })->toArray();
            $couchdbDoc->setContent($newContent);
            // 4. Sauvegarde en base
            $couchdbDoc=$this->documentUpdate($couchdbDoc);
         }
      /**
       * @param object $entity
       * @return CouchdbItem
       */
      public function itemRead(object $entity): CouchdbItem {
         $id =$entity->getId();
         $class = get_class($entity);
         // 1. Récupération Document
         $couchdbDoc = $this->documentRead($class);
         // 2. Récupération du contenu
         $content = $couchdbDoc->getContent();
         // 3. Récupération de l'item
         return $couchdbDoc->getItem($id);
      }
      /**
       * @param object $entity
       * @return object
       */
      public function itemUpdate(object $entity): object {
         $this->logger->info(__CLASS__.'/'.__METHOD__);
         $newEventPreUpdate = new CouchdbItemPreUpdateEvent($entity);
         $this->eventDispatcher->dispatch($newEventPreUpdate,Events::preUpdate);
         return $newEventPreUpdate->getEntity();
      }
   //endregion
}