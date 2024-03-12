<?php

namespace App\EventSubscriber;

use App\Entity\Logistics\Order\Receipt;
use App\Entity\Purchase\Order\Order as PurchaseOrder;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;
use Psr\Log\LoggerInterface as Logger;

/**
 * Ce fichier à pour but de gérer les transitions des objets PurchaseOrder et
 * Receipt
 ** PurchaseOrder -> validate -> PurchaseOrderItem
 ** PurchaseOrder -> pay -> PurchaseOrderItem
 ** Receipt -> validate -> PurchaseOrderItem
 */
class PurchaseOrderSubscriber implements EventSubscriberInterface
{
    private $workflowRegistry;


    public function __construct(Registry $workflowRegistry, private readonly Logger $logger)
    {
        $this->workflowRegistry = $workflowRegistry;
    }

    /**
     * Cette méthode permet de gérer les transitions des objets PurchaseOrder
     * "validate" et "pay"
     * @param Event $event
     * @return void
     */
    public function onWorkflowPurchaseOrderTransition(Event $event): void
    {
        $object = $event->getSubject();
        $workflowName = $event->getWorkflowName();
        $transition = $event->getTransition();
        $transitionName = $transition->getName();

        if ($object instanceof PurchaseOrder && $workflowName === 'purchase_order') {
            $po_blocker = $object->getEmbBlocker()->getstate();
            /**
             * Si la transition est "validate" et que le blocker est "enabled" alors on applique la transition "validate" à chaque item de la commande
             */
            if ($transitionName === 'validate' && $po_blocker === 'enabled') {
                $this->applyTransitionToItems($object->getItems(), 'purchase_order_item', 'validate', $this->workflowRegistry);
            }
            /**
             * Si la transition est "pay" et que le blocker est "enabled" alors on applique la transition "pay" à chaque item de la commande
             */
            if ($transitionName === 'pay' && $po_blocker === 'enabled') {
                $items = $object->getItems();
                foreach ($items as $item) {
                    $blocker = $item->getEmbBlocker()->getstate();
                    $state = $item->getEmbState()->getstate();
                    /**
                     * Si le blocker de l'item est "enabled" et que son état est différent de "paid" alors on applique la transition "pay" à l'item
                     */
                    if ($blocker === 'enabled' && $state !== 'paid') {
                        $this->applyTransition($item, 'purchase_order_item', 'pay', $this->workflowRegistry);
                    }
                }
            }
        }
    }

    public function onWorkflowReceiptTransition(Event $event): void
    {
        $object = $event->getSubject();
        $workflowName = $event->getWorkflowName();
        $transition = $event->getTransition();
        $transitionName = $transition->getName();

        if ($object instanceof Receipt && $workflowName === 'receipt') {
            $receipt_blocker = $object->getEmbBlocker()->getstate();
            $purchaseOrderItem = $object->getItem();
            /**
             * Si la transition est "validate" et que le blocker est "enabled" alors on continue
             */
            if ($transitionName === 'validate' && $receipt_blocker === 'enabled') {
                $received_quantity = $object->getQuantity();
                if (!isset($purchaseOrderItem)) {
                    $this->logger->warning(
                        `La réception ${$object->getId()} n'est associé à aucun item de commande`,
                        ['receipt' => $object, 'workflow' => 'receipt', 'transition' => 'validate', 'blocker' => $receipt_blocker, 'purchaseOrderItem' => $purchaseOrderItem]
                    );
                    return;
                }
                $purchaseOrderItemState = $purchaseOrderItem->getEmbState()->getState();
                $purchased_quantity_confirmed = $purchaseOrderItem->getConfirmedQuantity();
                $purchased_received_quantity = $purchaseOrderItem->getReceivedQuantity();

                // On ajoute la quantité reçue à la quantité déjà reçue
                $purchased_received_quantity->add($received_quantity);

                if ($purchased_received_quantity->isGreaterThanOrEqual($purchased_quantity_confirmed)) {
                    /**
                     * Si la quantité reçue est supérieure ou égale à la quantité confirmée
                     * et que l'état de l'item est "agreed"
                     * alors on applique la transition "receive" à l'item de commande
                     */
                    if ($purchaseOrderItemState === 'agreed' || $purchaseOrderItemState === 'partially_received') {
                        $this->applyTransition($purchaseOrderItem, 'purchase_order_item', 'receive', $this->workflowRegistry);
                    } else {
                        $this->logger->warning(
                            `La réception ${$object->getId()} est associé à l'item de commande ${$purchaseOrderItem->getId()} dans le statut non compatible $purchaseOrderItemState`,
                            ['receipt' => $object, 'workflow' => 'receipt', 'transition' => 'validate', 'blocker' => $receipt_blocker, 'purchaseOrderItem' => $purchaseOrderItem]
                        );
                    }
                } else {
                    /**
                     * Si la quantité reçue est inférieure à la quantité confirmée
                     * et que l'état de l'item est "agreed"
                     * alors on applique la transition "partially_receive" à l'item de commande
                     */
                    if ($purchaseOrderItemState === 'agreed') {
                        $this->applyTransition($purchaseOrderItem, 'purchase_order_item', 'partially_receive', $this->workflowRegistry);
                    } else {
                        if ($purchaseOrderItemState !== 'partially_received') {
                            $this->logger->warning(
                                `La réception ${$object->getId()} est associé à l'item de commande ${$purchaseOrderItem->getId()} dans le statut non compatible $purchaseOrderItemState`,
                                ['receipt' => $object, 'workflow' => 'receipt', 'transition' => 'validate', 'blocker' => $receipt_blocker, 'purchaseOrderItem' => $purchaseOrderItem]
                            );
                        }
                    }
                }
            }
        }
    }


    /**
     * Cette méthode permet de mettre à jour l'état d'une collection d'objets
     * @param ArrayCollection $objects
     * @param string          $workflowName
     * @param string          $transitionName
     * @param Registry        $workflowRegistry
     * @return void
     */
    public
    function applyTransitionToItems(ArrayCollection $objects, string $workflowName, string $transitionName, Registry $workflowRegistry): void
    {
        foreach ($objects as $object) {
            $this->applyTransition($object, $workflowName, $transitionName, $workflowRegistry);
        }
    }

    /**
     * Cette méthode permet d'appliquer une transition à un objet
     * @param $object
     * @param $workflowName
     * @param $transitionName
     * @param $workflowRegistry
     * @return void
     */
    public
    function applyTransition($object, $workflowName, $transitionName, $workflowRegistry): void
    {
        $workflow = $workflowRegistry->get($object, $workflowName);

        if ($workflow->can($object, $transitionName)) {
            $workflow->apply($object, $transitionName);
        }
    }

    public
    static function getSubscribedEvents(): array
    {
        return [
            'workflow.purchase_order.transition' => 'onWorkflowPurchaseOrderTransition',
            'workflow.receipt.transition' => 'onWorkflowReceiptTransition'
        ];
    }
}
