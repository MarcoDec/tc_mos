<?php

namespace App\Service;

use App\Attribute\Couchdb\Document;
use App\Entity\Couchdb\CouchdbDocument;
use App\Entity\Couchdb\CouchdbItem;
use App\Event\Couchdb\Events;
use App\Event\Couchdb\Item\CouchdbItemPrePersistEvent;
use App\Event\Couchdb\Item\CouchdbItemPreRemoveEvent;
use App\Event\Couchdb\Item\CouchdbItemPreUpdateEvent;
use Doctrine\CouchDB\CouchDBClient;
use Doctrine\CouchDB\HTTP\HTTPException;
use Doctrine\CouchDB\HTTP\Response;
use Exception;
use HaydenPierce\ClassFinder\ClassFinder;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\DataCollector\CouchdbLogItem;

class CouchDBManager
{
   private CouchDBClient $client;
   public array $actions;
   public function __construct(private string $couchUrl, private string $couchDBName, private EventDispatcherInterface $eventDispatcher, private LoggerInterface $logger) {
      $this->init();
   }

   private function init() {
      $this->client = CouchDBClient::create([
         'url'=> $this->couchUrl,
         'dbname'=> $this->couchDBName
      ]);
      $this->actions=[];
   }

   /**
    * @throws HTTPException
    */
   public function createDatabase():void {
      $this->client->createDatabase($this->client->getDatabase());
   }

   public function allDocs(): Response {
      $couchLog = new CouchdbLogItem("all documents",'allDocs',__METHOD__);
      $time = date_format(new \DateTime('now'),"Y/m/d - H:i:s:u");
      try {
         $allDoc =$this->client->allDocs();
         $couchLog->setDetail("[Appel client->allDocs() Ok]");
      } catch (Exception $e) {
         $couchLog->setDetail("[Appel client->allDocs() KO]\t".$e->getMessage());
         $couchLog->setErrors(true);
         $allDoc = new Response(500,$e->getTrace(),$e->getMessage());
      }
      $this->actions[$time]= $couchLog;
      return $allDoc;
   }

   /**
    * @return array
    * @throws Exception
    * Retourne la liste des noms(id) des documents
    */
   public function getDocList():array {
      $time = date_format(new \DateTime('now'),"Y/m/d - H:i:s:u");
      $couchLog = new CouchdbLogItem("all documents","getDocList",__METHOD__);
      try {
         $rows =$this->allDocs()->body["rows"];
         $couchLog->setDetail(count($rows)." document(s) found");
         $this->actions[$time]=$couchLog;
         return collect($rows)->map(function($doc){ return $doc["id"]; })->toArray();
      } catch (Exception $e) {
         $couchLog->setDetail($e->getMessage());
         $couchLog->setErrors(true);
         $this->actions[$time]=$couchLog;
         throw new Exception("La base ".$this->couchDBName." n'existe pas",0);
      }
   }

   /**
    * @return array
    * @throws Exception
    * Retourne la liste des Entités qui ont l'attribut Couchdb\Document
    */
   public function getCouchdbDocuments():array {
      $time = date_format(new \DateTime('now'),"Y/m/d - H:i:s:u");
      $couchLog = new CouchdbLogItem("Entity CouchdbDocument documents","getCouchdbDocuments (List)",__METHOD__);
      try{
         $allClasses = ClassFinder::getClassesInNamespace('App\Entity',ClassFinder::RECURSIVE_MODE);
         $filteredClass = collect($allClasses)->filter(function($class){
            $reflexionClass=new \ReflectionClass($class);
            return count($reflexionClass->getAttributes(Document::class))>0;
         })->toArray();
         $couchLog->setDetail(count($filteredClass)." CouchdbDocument found");
      } catch (Exception $e) {
         $filteredClass=[];
         $couchLog->setDetail($e->getMessage());
         $couchLog->setErrors(true);
      }
      $this->actions[$time]=$couchLog;
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
      $time = date_format(new \DateTime('now'),"Y/m/d - H:i:s:u");
      $couchLog = new CouchdbLogItem($dbDoc,'documentGetRev',__METHOD__);
      try {
         foreach ($this->allDocs()->body['rows'] as $doc) {
            if ($doc["id"]==$dbDoc) {
               $couchLog->setDetail("Document ".$dbDoc." found");
               $this->actions[$time]=$couchLog;
               return $doc["value"]["rev"];
            }
         }
      } catch (Exception $e){
         $couchLog->setDetail($e->getMessage());
         $couchLog->setErrors(true);
         $this->actions[$time]=$couchLog;
      }
      return "";
   }

   //region CRUD document
      /**
       * Créer un nouveau document et recharge le document une fois créé
       */
      public function documentCreate(array $content): ?CouchdbDocument
      {
         $time = date_format(new \DateTime('now'),"Y/m/d - H:i:s:u");
         $couchLog = new CouchdbLogItem($content['id'],CouchdbLogItem::METHOD_DOCUMENT_CREATE,__METHOD__);
         try {
            $id_rev =$this->client->postDocument($content);
            $couchLog->setDetail("Document ".$id_rev[0]." created (".$id_rev[1].")");
            $this->actions[$time]=$couchLog;
            return $this->documentRead($id_rev[0]);
         } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time]=$couchLog;
            return null;
         }
      }
      /**
       * Supprime un document
       */
      public function documentDelete($id, $rev) {
         $time = date_format(new \DateTime('now'),"Y/m/d - H:i:s:u");
         $couchLog = new CouchdbLogItem($id,CouchdbLogItem::METHOD_DOCUMENT_DELETE,__METHOD__);
         try {
            $this->client->deleteDocument($id,$rev);
            $couchLog->setDetail("Document ".$id." (".$rev.") deleted");
            $this->actions[$time]=$couchLog;
         } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time]=$couchLog;
         }
      }
      /**
       * Charge un document existant
       * @param $id
       * @return CouchdbDocument|null
       */
      public function documentRead($id): ?CouchdbDocument {
         $time = date_format(new \DateTime('now'),"Y/m/d - H:i:s:u");
         $couchLog = new CouchdbLogItem($id,CouchdbLogItem::METHOD_DOCUMENT_READ,__METHOD__);
         try {
            $coudbDoc =new CouchdbDocument($this->client->findDocument($id));
            $couchLog->setDetail("Document ".$id." loaded");
            $this->actions[$time]=$couchLog;
            return $coudbDoc;
         } catch (Exception $e){
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time]=$couchLog;
            return null;
         }
      }

   /**
    * Met à jour un document, cela incrémente sa révision
    * @param CouchdbDocument $couchdbDocument
    * @return CouchdbDocument|null [$id,$rev]
    */
      public function documentUpdate(CouchdbDocument $couchdbDocument): ?CouchdbDocument {
         $time = date_format(new \DateTime('now'),"Y/m/d - H:i:s:u");
         $couchLog = new CouchdbLogItem($couchdbDocument->getId(),CouchdbLogItem::METHOD_DOCUMENT_UPDATE,__METHOD__);
         $this->logger->info(__METHOD__);
         try {
            list($id, $rev) = $this->client->putDocument(['content'=>$couchdbDocument->getContent()], $couchdbDocument->getId(), $couchdbDocument->getRev());
            $couchLog->setDetail("Document ".$id." updated");
            $this->actions[$time]=$couchLog;
            return $couchdbDocument;
         } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time]=$couchLog;
            return null;
         }
      }
   //endregion
   //region CRUD item
   /**
    * Crée un item dans la base via l'évènement CouchdbPrePersistEvent
    * @param  object|null  $entity
    * @return object|null
    */
      public function itemCreate(?object $entity): ?object {
         $time = date_format(new \DateTime('now'),"Y/m/d - H:i:s:u");
         $class = $entity===null?"null":get_class($entity);
         $couchLog = new CouchdbLogItem($class,CouchdbLogItem::METHOD_CREATE,__METHOD__);
         $this->logger->info(__CLASS__.'/'.__METHOD__);
         try {
            $newEventPrePersist = new CouchdbItemPrePersistEvent($entity);
            $this->eventDispatcher->dispatch($newEventPrePersist,Events::prePersist);
            $couchLog->setDetail("Document ".$class." CouchdbPrePersistEvent lancé");
            $this->actions[$time]=$couchLog;
            return $entity;
         } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time]=$couchLog;
            return null;
         }
      }

      /**
       * Supprime un item dans la base
       * @param object|null $entity
       */
      public function itemDelete(?object $entity):void {
         $time = date_format(new \DateTime('now'),"Y/m/d - H:i:s:u");
         $this->logger->info(__CLASS__.'/'.__METHOD__);
         $class = $entity===null?"null":get_class($entity);
         $couchLog = new CouchdbLogItem($class,CouchdbLogItem::METHOD_DELETE,__METHOD__);
         try {
            $newEventPreRemove = new CouchdbItemPreRemoveEvent($entity);
            $this->eventDispatcher->dispatch($newEventPreRemove,Events::preRemove);
            $couchLog->setDetail("Document ".$class." item ".$entity->getId()." CouchdbPreRemoveEvent lancé");
            $this->actions[$time]=$couchLog;
         } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time]=$couchLog;
         }
         }
      /**
       * @param object $entity
       * @return CouchdbItem|null
       */
      public function itemRead(object $entity): ?CouchdbItem {
         $time = date_format(new \DateTime('now'),"Y/m/d - H:i:s:u");
         $id =$entity->getId();
         $class = get_class($entity);
         $couchLog = new CouchdbLogItem($class,CouchdbLogItem::METHOD_READ,__METHOD__);
         try {
            // 1. Récupération Document
            $couchdbDoc = $this->documentRead($class);
            // 2. Récupération du contenu
            $content = $couchdbDoc->getContent();
            // 3. Récupération de l'item
            $item = $couchdbDoc->getItem($id);
            $couchLog->setDetail(__METHOD__." Document => ".$class." item => ".$entity->getId());
            $this->actions[$time]=$couchLog;
            return $item;
         } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time]=$couchLog;
            return null;
         }
      }
      /**
       * @param object $entity
       * @return object|null
       */
      public function itemUpdate(object $entity): ?object {
         $time = date_format(new \DateTime('now'),"Y/m/d - H:i:s:u");
         $this->logger->info(__CLASS__.'/'.__METHOD__);
         $class = get_class($entity);
         $couchLog = new CouchdbLogItem($class,CouchdbLogItem::METHOD_UPDATE,__METHOD__);
         try {
            $newEventPreUpdate = new CouchdbItemPreUpdateEvent($entity);
            $this->eventDispatcher->dispatch($newEventPreUpdate,Events::preUpdate);
            $item =$newEventPreUpdate->getEntity();
            $couchLog->setDetail("Document ".$class." item ".$entity->getId()." CouchdbPreUpdateEvent lancé");
            $this->actions[$time]=$couchLog;
            return $item;
         } catch (Exception $e){
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time]=$couchLog;
            return null;
         }
      }
   //endregion

}