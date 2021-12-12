<?php

namespace App\Entity\Couchdb;

use App\Entity\Entity;
use ReflectionClass;
use ReflectionException;

/**
 * Objet représentant un élément contenu dans un CouchDB Document.
 */
class Item {
    private string $class;
   /**
    * @var mixed[]
    */
    private array $content;
    private string|int $id;

   /**
    * @param string $class
    * @param array<string,mixed> $content
    */
    public function __construct(string $class, array $content) {
        if (isset($content['id'])) {
            $this->id = $content['id'];
        } else {
            $this->id = 0;
        }

        $this->class = $class;
        $this->content = $content;
    }

   /**
    * @return string
    */
    public function getClass(): string {
        return $this->class;
    }

   /**
    * @return array<string,mixed>
    */
    public function getContent(): array {
        return $this->content;
    }

   /**
    * @return mixed
    */
    public function getId(): mixed {
        return $this->id;
    }

   /**
    * @param string $class
    * @return Item
    */
    public function setClass(string $class): self {
        $this->class = $class;
        return $this;
    }

   /**
    * @param array<string,mixed> $content
    * @return $this
    */
    public function setContent(array $content): self {
        $this->content = $content;
        return $this;
    }

    /**
     * @throws ReflectionException
     */
    public function setEntity(Entity $entity): void {
       /** @phpstan-ignore-next-line */
        $reflectionClass = new ReflectionClass($this->class);
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            $this->content[$property->getName()] = $entity->{$property->getName()};
        }
    }

    /**
     * @param int|string $id
     */
    public function setId(mixed $id): self {
        $this->id = $id;
        return $this;
    }
}
