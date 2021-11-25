<?php

namespace App\Entity\Couchdb;

use App\Entity\Entity;
use ReflectionClass;
use ReflectionException;

class CouchdbItem
{
   private string $id;
   private string $content;
   private string $class;

   public function __construct(string $class, array $content) {
      $this->id=$content['id'];
      $this->class=$class;
      $this->content = $content['content'];
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
    * @return mixed
    */
   public function getContent(): mixed
   {
      return $this->content;
   }

   /**
    * @param mixed|string $content
    */
   public function setContent(mixed $content): self
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
         $entity->{$property}=$this->content[$property];
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
         $this->content[$property]=$entity->{$property};
      }
   }
}