<?php

namespace App\Service;

use App\Attribute\Couchdb\Document;
use Doctrine\CouchDB\CouchDBClient;
use Doctrine\CouchDB\HTTP\HTTPException;
use Doctrine\CouchDB\HTTP\Response;
use HaydenPierce\ClassFinder\ClassFinder;

class CouchDBManager
{
   private CouchDBClient $client;

   public function __construct(private string $couchUrl, private string $couchDBName) {
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
   public function getDocList():array {
      try {
         $rows =$this->allDocs()->body["rows"];
         return collect($rows)->map(function($doc){ return $doc["id"]; })->toArray();;
      } catch (\Exception $e) {
         throw new \Exception("La base ".$this->couchDBName." n'existe pas",0);
      }
   }

   public function getCouchdbDocument():array {
      $allClasses = ClassFinder::getClassesInNamespace('App\Entity',ClassFinder::RECURSIVE_MODE);
      $filteredClass = collect($allClasses)->filter(function($class){
         $reflexionClass=new \ReflectionClass($class);
         return count($reflexionClass->getAttributes(Document::class))>0;
      })->toArray();
      return $filteredClass;
   }

   /**
    * @throws HTTPException
    */
   public function deleteDocument($id, $rev) {
      $this->client->deleteDocument($id,$rev);
   }

   public function getDocumentRev($dbDoc):string {
      foreach ($this->allDocs()->body['rows'] as $doc) {
         if ($doc["id"]==$dbDoc) {
            return $doc["value"]["rev"];
         }
      }
      return "";
   }

   /**
    * @throws HTTPException
    */
   public function postDocument(array $content): array
   {
      return $this->client->postDocument($content);
   }

}