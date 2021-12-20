<?php

namespace App\DataProvider\Couchdb;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Repository\Couchdb\AbstractRepository;
use App\Service\CouchDBManager;
use Exception;
use ReflectionException;

class CouchdbDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface, ItemDataProviderInterface
{

   /**
    * @param CouchDBManager $manager
    */
   public function __construct(private CouchDBManager $manager) {
   }

   /**
    * @param string $resourceClass
    * @param string|null $operationName
    * @param array $context
    * @return array
    * @throws ReflectionException
    */
   public function getCollection(string $resourceClass, string $operationName = null, array $context = []): array
   {
      $repoClassName = CouchDBManager::getRepositoryFromEntityClass($resourceClass);
      //$repoClassName=str_replace('Entity','Repository', $resourceClass).'Repository';
      /** @var AbstractRepository $repo */
      $repo = new $repoClassName($this->manager, $resourceClass);
      return $repo->findAll();
   }

   /**
    * @param string $resourceClass
    * @param string|null $operationName
    * @param array $context
    * @return bool
    */
   public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
   {
      $reflectionClass = new \ReflectionClass($resourceClass);
      if ($reflectionClass->isAbstract()) return false;
      return $this->manager->isCouchdbDocument(new $resourceClass());
   }

   /**
    * @param string $resourceClass
    * @param $id
    * @param string|null $operationName
    * @param array $context
    * @return mixed
    * @throws Exception
    */
   public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): mixed
   {
      $repoClassName = CouchDBManager::getRepositoryFromEntityClass($resourceClass);
      //$repoClassName=str_replace('Entity','Repository', $resourceClass).'Repository';
      /** @var AbstractRepository $repo */
      $repo = new $repoClassName($this->manager, $resourceClass);
      return $repo->find($id);
   }
}