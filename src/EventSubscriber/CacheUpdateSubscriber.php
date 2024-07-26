<?php
// src/EventSubscriber/CacheUpdateSubscriber.php

namespace App\EventSubscriber;

use Predis\Client;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use App\Entity\Project\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Selling\Order\ProductItem;
use App\Entity\Logistics\Stock\ProductStock;
use App\Entity\Project\Product\Nomenclature;
use App\Entity\Purchase\Component\Component;
use App\Entity\Logistics\Stock\ComponentStock;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\Purchase\Order\Item as PurchaseItem;
use App\Entity\Production\Manufacturing\Order as ManufacturingOrder;


class CacheUpdateSubscriber implements EventSubscriber
{
    private Client $redis;
    private string $cacheKeyStocks;
    private string $cacheKeySelling;
    private string $cacheKeymanufacturingOrders;
    private string $cacheKeyProducts;
    private string $cacheKeyCreationDate;
    private string $cacheKeyCreationDateComponent;
    private string $cachekeyComponentStock;
    private string $cachekeyPurchaseItem;
    private string $cacheKeyNomenclature = 'api_needs_nomenclature_%s';
    private string $cacheKeyComponent = 'api_needs_component_%s';
    private EntityManagerInterface $entityManager;

    public function __construct(
        Client $redis,
        EntityManagerInterface $entityManager,
        string $cacheKeyStocks,
        string $cacheKeySelling,
        string $cacheKeymanufacturingOrders,
        string $cacheKeyProducts,
        string $cacheKeyCreationDate,
        string $cacheKeyCreationDateComponent,
        string $cachekeyComponentStock,
        string $cachekeyPurchaseItem,
        string $cacheKeyNomenclature,
        string $cacheKeyComponent
    )
    {
        $this->redis = $redis;
        $this->entityManager = $entityManager;
        $this->cacheKeyStocks = $cacheKeyStocks;
        $this->cacheKeySelling = $cacheKeySelling;
        $this->cacheKeymanufacturingOrders = $cacheKeymanufacturingOrders;
        $this->cacheKeyProducts = $cacheKeyProducts;
        $this->cacheKeyCreationDate = $cacheKeyCreationDate;
        $this->cacheKeyCreationDateComponent = $cacheKeyCreationDateComponent;
        $this->cachekeyComponentStock = $cachekeyComponentStock;
        $this->cachekeyPurchaseItem = $cachekeyPurchaseItem;
        $this->cacheKeyNomenclature = $cacheKeyNomenclature;
        $this->cacheKeyComponent = $cacheKeyComponent;
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
            if (isset($changeSet['quantity.value']) || isset($changeSet['quantity.code']) || isset($changeSet['item']) ) {
                $this->updateCreationDateCache($this->cacheKeyStocks);
               $this->updateCacheValue($this->cacheKeyStocks, $entity, ['quantity','item']);
            }
        }
        if ($entity instanceof ProductItem) {
            if (isset($changeSet['confirmedQuantity.value']) || isset($changeSet['confirmedQuantity.code']) || isset($changeSet['confirmedDate']) ) {
            $this->updateCreationDateCache($this->cacheKeySelling);
           $this->updateCacheValue($this->cacheKeySelling, $entity, ['confirmedQuantity', 'confirmedDate']);
            }
        }
        if ($entity instanceof ManufacturingOrder) {
            if (isset($changeSet['product']) || isset($changeSet['quantityRequested.value']) || isset($changeSet['quantityRequested.code']) || isset($changeSet['manufacturingDate']) ) {
            $this->updateCreationDateCache($this->cacheKeymanufacturingOrders);
            $this->updateCacheValue($this->cacheKeymanufacturingOrders, $entity, ['product','quantityRequested', 'manufacturingDate']);
            }
        }
        if ($entity instanceof Product) {
            if (isset($changeSet['minStock.value']) || isset($changeSet['minStock.code'])) {
            $productId = $entity->getId();
            $this->updateCreationDateCache($this->cacheKeyProducts);
            $this->updateCacheValue($this->cacheKeyProducts, $entity, ['minStock']);
            }
        }
        if ($entity instanceof Nomenclature) {
            $productId = $entity->getProduct()->getId();
            $cacheKeyNomenclature = sprintf($this->cacheKeyNomenclature, $productId);;
            if (isset($changeSet['quantity.value']) || isset($changeSet['quantity.unit']) || isset($changeSet['code']) || isset($changeSet['component.id']) || isset($changeSet['product.id'])) { 
            $this->updateCreationDateCacheComponent($cacheKeyNomenclature);
            $this->updateCacheValue($cacheKeyNomenclature, $entity, ['quantity','code','component','product']);
            }
        }
        if ($entity instanceof ComponentStock) {
            if (isset($changeSet['quantity.value']) || isset($changeSet['quantity.code']) || isset($changeSet['item'])) {
                $this->updateCreationDateCacheComponent($this->cachekeyComponentStock);
                $this->updateCacheValue($this->cachekeyComponentStock, $entity, ['quantity','item']);
            }
        }
        if ($entity instanceof Component) {
            $componentId = $entity->getId(); 
            $cacheKeyComponent = sprintf($this->cacheKeyComponent, $componentId);;
            if (isset($changeSet['minStock.value']) || isset($changeSet['minStock.code']) || isset($changeSet['code']) || isset($changeSet['family.name']) || isset($changeSet['family.parent'])){
            $this->updateCreationDateCacheComponent($cacheKeyComponent);
            $this->updateCacheValue($cacheKeyComponent, $entity,['code','family','minstock'] );
            }
        }
        if ($entity instanceof PurchaseItem) {
            if (isset($changeSet['item']) || isset($changeSet['confirmedDate']) || isset($changeSet['confirmedQuantity.value']) || isset($changeSet['confirmedQuantity.code']) || isset($changeSet['receipts.quantity'])) {
                $this->updateCreationDateCacheComponent($this->cachekeyPurchaseItem);
                $this->updateCacheValue($this->cachekeyPurchaseItem, $entity, ['item','confirmedDate','confirmedQuantity','receipts.quantity']);
            }
        }
}
    public function updateCreationDateCache(string $cacheKey)
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
        if (isset($cacheCreationDates['value'][$cacheKey])) {
            $cacheCreationDates['value'][$cacheKey] = date('Y-m-d H:i:s');
        }
        // Stockez la nouvelle valeur dans Redis
        $this->redis->set($this->cacheKeyCreationDate, serialize($cacheCreationDates));
        return $cacheCreationDates;
    }

    public function updateCreationDateCacheComponent(string $cacheKey)
    {
        // Récupérer la valeur actuelle de la clé api_needs_creation_date
        $cacheItemCreationDateC = $this->redis->get($this->cacheKeyCreationDateComponent);
        // Désérialiser la valeur, en vérifiant si c'est null
        $unserializedData = unserialize($cacheItemCreationDateC);
        // Vérifier si la désérialisation a réussi
        if ($unserializedData === false) {
            // Si la désérialisation a échoué, initialiser un tableau vide
            $cacheCreationDatesC = [];
        } else {
            // Sinon, convertir l'objet désérialisé en tableau associatif
            $cacheCreationDatesC = (array) $unserializedData;
        }
        // Supprimer la clé 'metadata' du tableau si elle existe
        unset($cacheCreationDatesC['metadata']);
        // Mettre à jour la valeur de la clé spécifiée si elle existe
        if (isset($cacheCreationDatesC['value'][$cacheKey])) {
            $cacheCreationDatesC['value'][$cacheKey] = date('Y-m-d H:i:s');
        }
        // Stockez la nouvelle valeur dans Redis
        $this->redis->set($this->cacheKeyCreationDateComponent, serialize($cacheCreationDatesC));
        return $cacheCreationDatesC;
    }

    private function updateCacheValue(string $cacheKey, $entity, array $propertiesToUpdate)
    {
        // Récupérer la valeur actuelle de la clé de cache
        $cacheItem = $this->redis->get($cacheKey);

        // Désérialiser la valeur, en vérifiant si c'est null
        $cachedData = unserialize($cacheItem);
        // Vérifier si la désérialisation a réussi
        if ($cachedData === false) {
            // Si la désérialisation a échoué, initialiser un tableau vide
            $cachedData = [];
        } else {
            // Sinon, convertir l'objet désérialisé en tableau associatif
            $cachedData = (array) $cachedData;
        }
        // Supprimer la clé 'metadata' du tableau si elle existe
        unset($cachedData['metadata']);
        $dataArray = $cachedData['value'];
        if (isset($cachedData['value'])) {
            foreach ($dataArray as $item) {
                if ($item->getId() === $entity->getId()) {
                    // Mettre à jour les propriétés spécifiées de l'entité
                    foreach ($propertiesToUpdate as $property) {
                        $setter = 'set' . ucfirst($property);
                        if (method_exists($item, $setter)) {
                            $value = $entity->{'get' . ucfirst($property)}();
                            $item->$setter($value);
                        }
                    }
                    break; // Sortir de la boucle une fois la mise à jour effectuée
                }
            }
        }
        //dump(($cachedData));
        // Stockez la nouvelle valeur dans Redis après sérialisation
        $this->redis->set($cacheKey, serialize($cachedData));
    }

}
