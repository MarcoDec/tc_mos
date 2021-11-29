<?php

namespace App\Entity\Couchdb;

use App\Entity\Entity;
use ReflectionClass;
use ReflectionException;

class CouchdbItem
{
   private string $id;
   private array $content;
   private string $class;

   public function __construct(string $class, array $content) {
      if (isset($content['id'])) {
         $this->id = $content['id'];
      } else {
         $this->id = 0;
      }

      $this->class=$class;
      $this->content = $content;
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
    * @return array
    */
   public function getContent(): array
   {
      return $this->content;
   }

   /**
    * @param array $content
    */
   public function setContent(array $content): self
   {
      $this->content = $content;
      return $this;
   }

   /**
    * @return string
    */
   public function getClass(): string
   {
      return $this->class;
   }

   /**
    * @param string $class
    * @return CouchdbItem
    */
   public function setClass(string $class): self
   {
      $this->class = $class;
      return $this;
   }

   /**
    * @throws ReflectionException
    */
   public function getEntity() {
      $entity = new $this->class();
      $reflectionClass = new ReflectionClass($this->class);
      $properties = $reflectionClass->getProperties();
      foreach ($properties as $property) {
         $entity->{$property->getName()}=$this->content[$property->getName()];
      }
      return $entity;
   }

   /**
    * @throws ReflectionException
    */
   public function setEntity(Entity $entity) {
      $reflectionClass = new ReflectionClass($this->class);
      $properties = $reflectionClass->getProperties();
      foreach ($properties as $property) {
         $this->content[$property->getName()]=$entity->{$property->getName()};
      }
   }
}