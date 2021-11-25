<?php

namespace App\Entity\Couchdb;


use Doctrine\CouchDB\HTTP\Response;

class CouchdbDocument
{
   private string $id;
   private string $rev;
   private array $content;

   public function __construct(Response $couchdbDocResponse){
      $this->id = $couchdbDocResponse->body['_id'];
      $this->rev = $couchdbDocResponse->body['_rev'];
      $this->content=[];
      $content= $couchdbDocResponse->body['content'];
      foreach ($content as $item) {
         $this->content[] = new CouchdbItem($this->id,$item);
      }
   }

   /**
    * @return mixed
    */
   public function getId(): mixed
   {
      return $this->id;
   }

   /**
    * @param mixed|string $id
    */
   public function setId(mixed $id): self
   {
      $this->id = $id;
      return $this;
   }

   /**
    * @return mixed|string
    */
   public function getRev(): mixed
   {
      return $this->rev;
   }

   /**
    * @param mixed|string $rev
    */
   public function setRev(mixed $rev): self
   {
      $this->rev = $rev;
      return $this;
   }

   /**
    * @return array
    */
   public function getContent(): array
   {
      return $this->content;
   }

   /**
    * @param array $content
    * @return CouchdbDocument
    */
   public function setContent(array $content): self
   {
      $this->content = $content;
      return $this;
   }

   /**
    * @param $id
    * @return CouchdbItem
    */
   public function getItem($id): CouchdbItem {
      $itemData = collect($this->content)->filter(function($item) use ($id) {
         return $item['id']===$id;
      })->first();
      return new CouchdbItem($this->id,$itemData);
   }
}