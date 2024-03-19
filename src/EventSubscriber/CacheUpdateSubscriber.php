<?php
// src/EventSubscriber/CacheUpdateSubscriber.php

namespace App\EventSubscriber;

use Predis\Client;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Selling\Order\ProductItem;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use App\Entity\Logistics\Stock\ProductStock;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CacheUpdateSubscriber implements EventSubscriber
{
    private Client $redis;
    private string $cacheKeyStocks;
    private string $cacheKeyCreationDate;

    public function __construct(Client $redis, string $cacheKeyStocks, string $cacheKeyCreationDate)
    {
        $this->redis = $redis;
        $this->cacheKeyStocks = $cacheKeyStocks;
        $this->cacheKeyCreationDate = $cacheKeyCreationDate;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->updateCache($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->updateCache($args);
    }

    private function updateCache(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof ProductStock) {
            $this->updateCreationDateCache($this->cacheKeyStocks);
        }
    }

    private function updateCreationDateCache(string $cacheKey)
    {
        // Récupérer la valeur actuelle de la clé api_needs_creation_date
        $cacheItemCreationDate = $this->redis->get($this->cacheKeyCreationDate);
        // Désérialiser la valeur, en vérifiant si c'est null
        $unserializedData = unserialize($cacheItemCreationDate);
        // Vérifier si la désérialisation a réussi
        if ($unserializedData === false) {
            // Si la désérialisation a échoué, initialiser un tableau vide
            $cacheCreationDates = [];
        } else {
            // Sinon, convertir l'objet désérialisé en tableau associatif
            $cacheCreationDates = (array) $unserializedData;
        }
        // Mettre à jour la valeur de la clé spécifiée si elle existe
        $cacheCreationDates['value'][$cacheKey] = date('Y-m-d H:i:s');
    
        $newCacheItemCreationDate = serialize($cacheCreationDates);
        // Stockez la nouvelle valeur dans Redis
        $this->redis->set($this->cacheKeyCreationDate, $newCacheItemCreationDate);
    }
    
}
