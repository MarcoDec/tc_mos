<?php
// src/EventSubscriber/CacheUpdateSubscriber.php

namespace App\EventSubscriber;

use Predis\Client;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Selling\Order\ProductItem;
use App\Entity\Logistics\Stock\ProductStock;
use App\Entity\Production\Manufacturing\Order as ManufacturingOrder;
use App\Entity\Project\Product\Product;
use Doctrine\Persistence\Event\LifecycleEventArgs;


class CacheUpdateSubscriber implements EventSubscriber
{
    private Client $redis;
    private string $cacheKeyStocks;
    private string $cacheKeySelling;
    private string $cacheKeymanufacturingOrders;
    private string $cacheKeyProducts;
    private string $cacheKeyCreationDate;
    private EntityManagerInterface $entityManager;


    public function __construct(Client $redis,EntityManagerInterface $entityManager, string $cacheKeyStocks, string $cacheKeySelling, string $cacheKeymanufacturingOrders, string $cacheKeyProducts, string $cacheKeyCreationDate)
    {
        $this->redis = $redis;
        $this->entityManager = $entityManager;
        $this->cacheKeyStocks = $cacheKeyStocks;
        $this->cacheKeySelling = $cacheKeySelling;
        $this->cacheKeymanufacturingOrders = $cacheKeymanufacturingOrders;
        $this->cacheKeyProducts = $cacheKeyProducts;
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
        $changeSet = $this->entityManager->getUnitOfWork()->getEntityChangeSet($entity);

        if ($entity instanceof ProductStock) {
            if (isset($changeSet['quantity.value']) || isset($changeSet['quantity.code']) || isset($changeSet['warehouse']) || isset($changeSet['item']) ) {
                $this->updateCreationDateCache($this->cacheKeyStocks);
            }
        }
        if ($entity instanceof ProductItem) {
            if (isset($changeSet['confirmedQuantity.value']) || isset($changeSet['confirmedQuantity.code']) || isset($changeSet['order']) || isset($changeSet['confirmedDate']) ) {
            $this->updateCreationDateCache($this->cacheKeySelling);
            }
        }
        if ($entity instanceof ManufacturingOrder) {
            if (isset($changeSet['product']) || isset($changeSet['quantityRequested.value']) || isset($changeSet['quantityRequested.code']) || isset($changeSet['manufacturingDate']) ) {
            $this->updateCreationDateCache($this->cacheKeymanufacturingOrders);
            }
        }
        if ($entity instanceof Product) {
            if (isset($changeSet['minStock.value']) || isset($changeSet['minStock.code'])) {
            $this->updateCreationDateCache($this->cacheKeyProducts);
        }
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
        // Supprimer la clé 'metadata' du tableau si elle existe
        unset($cacheCreationDates['metadata']);

        // Mettre à jour la valeur de la clé spécifiée si elle existe
        $cacheCreationDates['value'][$cacheKey] = date('Y-m-d H:i:s');
    
        $newCacheItemCreationDate = serialize($cacheCreationDates);
        // Stockez la nouvelle valeur dans Redis
        $this->redis->set($this->cacheKeyCreationDate, $newCacheItemCreationDate);
    }
}
