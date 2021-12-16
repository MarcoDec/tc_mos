<?php

namespace App\Repository\Couchdb;

use App\Service\CouchDBManager;
use Exception;
use ReflectionException;

abstract class AbstractRepository
{

   public function __construct(private CouchDBManager $manager, private string $className) {
   }

   /**
    * @return array $this->className[]
    * @throws ReflectionException
    */
   public function getAll(): array {
      $className = $this->className;
      $couchdbDocument =$this->manager->documentRead($className);
      if ($couchdbDocument === null) return [];
      $content = $couchdbDocument->getContent();
      $all=[];
      foreach ($content as $item) {
         $all[]= $this->manager->convertArrayToEntity($item,$this->className);;
      }
      return $all;
   }

   /**
    * @throws ReflectionException
    * @return array $this->className[]
    */
   public function findAll() : array {
      return $this->getAll();
   }

   /**
    * @param string $uuid
    * @return mixed
    * @throws Exception
    */
   public function find(string $uuid): mixed {
      $couchdbDocument =$this->manager->documentRead($this->className);
      $item=$couchdbDocument->getItem($uuid)->getContent();
      return $this->manager->convertArrayToEntity($item, $this->className);
   }

   /**
    * @param mixed $entity
    * @return void
    * @throws Exception
    */
   public function persist(mixed $entity) {
      $couchdbItem = $this->manager->itemRead($entity);
      if ($couchdbItem===null) {
         $this->manager->itemCreate($entity);
      } else {
         $this->manager->itemUpdate($entity);
      }
   }

   /**
    * @param mixed $entity
    * @return void
    * @throws Exception
    */
   public function remove(mixed $entity) {
      $couchdbItem = $this->manager->itemRead($entity);
      if ($couchdbItem === null) throw new Exception("Impossible to remove an unexisting item in the couchdbDatabase");
      else $this->manager->itemDelete($entity);
   }
}