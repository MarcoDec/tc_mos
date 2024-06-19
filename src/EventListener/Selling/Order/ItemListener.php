<?php

namespace App\EventListener\Selling\Order;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Embeddable\Measure;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Selling\Order\Item;
use App\Entity\Selling\Order\Order;
use App\Entity\Selling\Order\ProductItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ItemListener implements EventSubscriberInterface
{
    /*
     * Cette fonction est appelée après la création d'un objet Item.
     * Elle a pour but de :
     *    - mettre à jour le prix de vente (unitaire) associée à l'item en fonction de la grille de prix client
     *    - mettre à jour le prix de vente total de l'item en fonction du prix unitaire et de la quantité
     *    - mettre à jour le prix de vente total de la commande en fonction du prix de vente total de l'item
     * @param Item $item
     */
    public function postPersist(ViewEvent $event): void
    {
        if (!($item = $event->getControllerResult()) instanceof Item || ($method = $event->getRequest()->getMethod()) !== 'POST') {
            return;
        }
        // Si l'item est un produit alors on récupère la grille de prix associée
        // au client de la commande et on met à jour le prix de vente unitaire
        // et le prix de vente total de l'item
        if ($item instanceof ProductItem) {
            $this->updateProductPrice($item);
        }
    }

    private function updateProductPrice(ProductItem $item): void
    {
        // On ne met les prix à jour automatiquement que si le statut de l'item est draft ou agreed
        if (!in_array($item->getEmbState()->getState(), ['draft', 'agreed'])) {
            return;
        }
        // On ne met à jour les prix que si l'item est associé à un produit et à une commande parente
        if ($item->getItem() === null || $item->getParentOrder() === null) {
            return;
        }
        /** @var Order $parentOrder */
        $parentOrder = $item->getParentOrder();
        // On ne peut mettre à jour les prix que si la commande parente est associée à un client
        if ($parentOrder->getCustomer() === null) {
            return;
        }
        $customer = $parentOrder->getCustomer();
        // On ne peut mettre à jour les prix que si le client est associé à une grille de prix
        if ($customer->getProductCustomers()->isEmpty()) {
            return;
        }
        $priceGrids = $customer->getProductCustomers();
        // On ne peut mettre à jour les prix que s'il existe une grille de prix pour le produit associé à l'item
        if (!$priceGrids->exists(fn($priceGrid) => $priceGrid->getProduct() === $item->getItem())) {
            return;
        }
        $priceGrid = $priceGrids->filter(fn($priceGrid) => $priceGrid->getProduct() === $item->getItem())->first();
        // On ne peut mettre à jour les prix que si la quantité de l'item est définie
        if ($item->getRequestedQuantity()->getValue() === null || $item->getRequestedQuantity()->getCode() === null) {
            return;
        }
        /*
         *  La quantité de l'item à considérer pour récupérer le prix dépends de son statut embState
         */
        $currentItemStatus = $item->getEmbState()->getState();
        $quantity = match ($currentItemStatus) {
            'draft' => $item->getConfirmedQuantity(),
            'agreed' => $item->getRequestedQuantity(),
            default => throw new \LogicException('Unknown item status ' . $currentItemStatus),
        };
        $bestPrice = $priceGrid->getBestPrice($quantity);
        $item->setPrice($bestPrice);
        $totalPrice = new Measure();
        $totalPrice->setCode($bestPrice->getCode());
        $totalPrice->setValue($bestPrice->getValue() * $item->getRequestedQuantity()->getValue());
        $item->setTotalItemPrice($totalPrice);
        /** @var Order $order */
        $order = $item->getParentOrder();
        // Si l'item est de type forecast alors on ajoute le prix total de l'item au prix total de la commande
        $order->updateTotalSellingPrice();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'api_platform.post_deserialize' => ['postPersist']
        ];
    }
}