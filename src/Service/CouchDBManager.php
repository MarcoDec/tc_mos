<?php

namespace App\Service;

use App\Attribute\Couchdb\Document;
use App\Entity\Couchdb\CouchdbDocument;
use App\Entity\Couchdb\CouchdbItem;
use App\Entity\Entity;
use App\Event\CouchdbPostPersistEvent;
use App\Event\CouchdbPrePersistEvent;
use Doctrine\CouchDB\CouchDBClient;
use Doctrine\CouchDB\HTTP\HTTPException;
use Doctrine\CouchDB\HTTP\Response;
use HaydenPierce\ClassFinder\ClassFinder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CouchDBManager
{
   private CouchDBClient $client;

   public function __construct(private string $couchUrl, private string $couchDBName, private EventDispatcherInterface $eventDispatcher) {
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
    * @throws \Exception
    * Retourne la liste des noms(id) des documents
    */
   public function getDocList():array {
      try {
         $rows =$this->allDocs()->body["rows"];
         return collect($rows)->map(function($doc){ return $doc["id"]; })->toArray();;
      } catch (\Exception $e) {
         throw new \Exception("La base ".$this->couchDBName." n'existe pas",0);
      }
   }

   /**
    * @return array
    * @throws \Exception
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
    * @param Entity $entity
    * @return bool
    * Retourne Vraie si l'entité est d'une classe comportant l'attribut Couchdb/Document
    */
   public function isCouchdbDocument(Entity $entity):bool {
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
       * @return array [$id,$rev]
       * @throws HTTPException
       */
      public function documentUpdate(CouchdbDocument $couchdbDocument): CouchdbDocument {
         list($id, $rev) = $this->client->putDocument($couchdbDocument->getContent(), $couchdbDocument->getId(), $couchdbDocument->getRev());
         return $this->documentRead($id);
      }
   //endregion
   //region CRUD item
      /**
       * Crée un item dans la base
       * @param Entity $entity
       * @return CouchdbItem
       * @throws HTTPException
       */
      public function itemCreate(Entity $entity): CouchdbItem {
         $class = get_class($entity);
         // 1. Récupération Document
         $couchdbDoc = $this->documentRead($class);
         // 2. Récupération du contenu
         $docContent = $couchdbDoc->getContent();
         // 3. Détermination du plus grand indice
         $maxId = collect($docContent)->max('id');
         $newId = $maxId++;
         // 4. Définition nouvel élément
         $reflectionClass = new \ReflectionClass($entity);
         $properties = $reflectionClass->getProperties();
         $content=[];
         foreach ($properties as $property) {
            $content[$property->getName()]=$entity->{$property->getName()};
         }
         $content['id']=$newId;
         // 5. Ajout nouvel élément dans couchdbDoc
         $docContent[]=$content;
         $couchdbDoc->setContent($docContent);
         // 6. Sauvegarde en base
         $this->documentUpdate($couchdbDoc);
         return new CouchdbItem($class, $content);
      }
      /**
       * Supprime un item dans la base
       * @param Entity $entity
       * @throws HTTPException
       */
      public function itemDelete(Entity $entity):void {
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
       * @param Entity $entity
       * @return CouchdbItem
       */
      public function itemRead(Entity $entity): CouchdbItem {
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
       * @param Entity $entity
       * @return CouchdbItem
       * @throws HTTPException
       */
      public function itemUpdate(Entity $entity): CouchdbItem {
         $id =$entity->getId();
         $class = get_class($entity);
         // 1. Récupération Document
         $couchdbDoc = $this->documentRead($class);
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
         })->toArray();
         $couchdbDoc->setContent($updatedContent);
         // 4. Sauvegarde en base
         $couchdbDoc=$this->documentUpdate($couchdbDoc);
         // 5. retour CouchdbItem
         return $couchdbDoc->getItem($id);
      }
   //endregion
}