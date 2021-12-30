<?php

namespace App\DataProvider\Couchdb;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Repository\Couchdb\AbstractRepository;
use App\Service\CouchDBManager;
use Exception;
use ReflectionClass;
use ReflectionException;

class CouchdbDataProvider implements ContextAwareCollectionDataProviderInterface, ItemDataProviderInterface, RestrictedDataProviderInterface {
    /**
     * @param CouchDBManager $manager
     */
    public function __construct(private CouchDBManager $manager) {
    }

    /**
     * @param array<mixed> $context
     *
     * @throws ReflectionException
     *
     * @return array<mixed>
     */
    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = []): array {
        $repoClassName = CouchDBManager::getRepositoryFromEntityClass($resourceClass);
        /** @var AbstractRepository $repo */
        $repo = new $repoClassName($this->manager, $resourceClass);
        return $repo->findAll();
    }

   /**
    * @param string $resourceClass
    * @param mixed $id
    * @param string|null $operationName
    * @param array<mixed> $context
    * @return mixed
    * @throws Exception
    */
    public function getItem(string $resourceClass, mixed $id, ?string $operationName = null, array $context = []): mixed {
        $repoClassName = CouchDBManager::getRepositoryFromEntityClass($resourceClass);
        /** @var AbstractRepository $repo */
        $repo = new $repoClassName($this->manager, $resourceClass);
        return $repo->find($id);
    }

   /**
    * @param string $resourceClass
    * @param string|null $operationName
    * @param array<mixed> $context
    * @return bool
    */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        if (class_exists($resourceClass)) {
            $reflectionClass = new ReflectionClass($resourceClass);
            if ($reflectionClass->isAbstract()) {
                return false;
            }
            return $this->manager->isCouchdbDocument(new $resourceClass());
        }
        return false;
    }
}
