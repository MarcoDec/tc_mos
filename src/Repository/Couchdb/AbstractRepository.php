<?php

namespace App\Repository\Couchdb;

use App\Service\CouchDBManager;
use Exception;
use ReflectionException;

abstract class AbstractRepository {
    public function __construct(private CouchDBManager $manager, private string $className) {
    }

    /**
     * @throws Exception
     */
    public function find(string $uuid): mixed {
        $couchdbDocument = $this->manager->documentRead($this->className);
        $item = $couchdbDocument->getItem($uuid)->getContent();
        return $this->manager->convertArrayToEntity($item, $this->className);
    }

    /**
     * @throws ReflectionException
     *
     * @return array $this->className[]
     */
    public function findAll(): array {
        return $this->getAll();
    }

    public function findBy(array $conditions): array {
        $couchdbDocument = $this->manager->documentRead($this->className);
        $items = $couchdbDocument->getItemsWhere($conditions);
        return collect($items)->map(fn ($item) => $this->manager->convertArrayToEntity($item, $this->className))->toArray();
    }

    /**
     * @throws ReflectionException
     *
     * @return array $this->className[]
     */
    public function getAll(): array {
        $className = $this->className;
        $couchdbDocument = $this->manager->documentRead($className);
        if ($couchdbDocument === null) {
            return [];
        }
        $content = $couchdbDocument->getContent();
        $all = [];
        foreach ($content as $item) {
            $all[] = $this->manager->convertArrayToEntity($item, $this->className);
        }
        return $all;
    }

    /**
     * @throws Exception
     */
    public function persist(mixed $entity): void {
        $couchdbItem = $this->manager->itemRead($entity);
        if ($couchdbItem === null) {
            $this->manager->itemCreate($entity);
        } else {
            $this->manager->itemUpdate($entity);
        }
    }

    /**
     * @param $entities
     */
    public function persistAll($entities): void {
        $this->manager->itemsUpdate($entities);
    }

    /**
     * @throws Exception
     */
    public function remove(mixed $entity): void {
        $couchdbItem = $this->manager->itemRead($entity);
        if ($couchdbItem === null) {
            throw new Exception('Impossible to remove an unexisting item in the couchdbDatabase');
        } else {
            $this->manager->itemDelete($entity);
        }
    }

    /**
     * @param $entities
     */
    public function removeAll($entities): void {
        $this->manager->itemsDelete($entities);
    }
}
