<?php

namespace App\DataPersister\Couchdb;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Repository\Couchdb\AbstractRepository;
use App\Service\CouchDBManager;
use Exception;

class CouchdbDataPersister implements ContextAwareDataPersisterInterface
{

   public function __construct(private CouchDBManager $manager) {
   }

   /**
    * @param $data
    * @param array $context
    * @return bool
    */
   public function supports($data, array $context = []): bool
   {
      return $this->manager->isCouchdbDocument($data);
   }


   /**
    * @param $data
    * @param array $context
    * @return void
    * @throws Exception
    */
   public function persist($data, array $context = [])
   {
      $className = get_class($data);
      $repoClassName = CouchDBManager::getRepositoryFromEntityClass($className);
      /** @var AbstractRepository $repo */
      $repo = new $repoClassName($this->manager,$className);
      $repo->persist($data);
   }

   /**
    * @param $data
    * @param array $context
    * @return void
    * @throws Exception
    */
   public function remove($data, array $context = [])
   {
      $className = get_class($data);
      $repoClassName = CouchDBManager::getRepositoryFromEntityClass($className);
      /** @var AbstractRepository $repo */
      $repo = new $repoClassName($this->manager,$className);
      $repo->remove($data);
   }
}