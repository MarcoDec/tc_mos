<?php

namespace App\EventSubscriber;

use App\Entity\Logistics\Order\Receipt;
use App\Entity\Production\Manufacturing\Expedition;
use App\Entity\Production\Manufacturing\Order as ManufacturingOrder;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Order\Order as PurchaseOrder;
use App\Entity\Purchase\Order\Item as PurchaseOrderItem;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Order\ComponentItem;
use App\Entity\Purchase\Supplier\Supplier;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Selling\Order\Item;
use App\Entity\Selling\Order\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;
use Psr\Log\LoggerInterface as Logger;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Quality\Reception\Check;

class BlockerSubscriber implements EventSubscriberInterface
{
    private Registry $workflowRegistry;
    private EntityManagerInterface $entityManager;

    public function __construct(Registry $workflowRegistry, private readonly Logger $logger, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->workflowRegistry = $workflowRegistry;
    }

    /**
     * Cette méthode permet de gérer les transitions du workflow blocker des entités Customer, Expedition, Supplier, Component, Product et Receipt
     * @param Event $event
     * @return void
     */
    public function onWorkflowBlockerTransition(Event $event): void
    {
        $object = $event->getSubject();
        $blockerWorkflowName = $event->getWorkflowName();
        $blockerTransition = $event->getTransition();
        $before = $blockerTransition->getFroms()[0];
        $after = $blockerTransition->getTos()[0];
        $blockerTransitionName = $blockerTransition->getName();

        /**
         * Dans le cas du workflow blocker de Customer
         */
        if ($object instanceof Customer && $blockerWorkflowName === 'blocker') {
            /**
             * Si un Customer est désactivé, on désactive les SellingOrders associées
             */
            if ($blockerTransitionName === 'disable' && $before === 'enabled' && $after === 'disabled') {
                $block = $object->getBlocker();
                $state = $object->getEmbState()->getState();
                if ($block === 'enabled') {
                    if ($state === 'draft') {
                        $this->logger->info(`customer est en état $state passe directement a close ` . $state);
                        $this->applyTransitionToWorkflow($object, 'customer', 'validate', $this->workflowRegistry);
                    }
                    $this->applyTransitionToWorkflow($object, 'customer', 'close', $this->workflowRegistry);
                }
                // si y a les etats blocked on les passe a enabled  dans le closer du sellingOrder
                $this->updateWorkflowState($object->getSellingOrders(), 'closer', 'unlock', $this->workflowRegistry);
                // on passe de enabled a closed dans le closer du sellingOrder
                $this->updateWorkflowState($object->getSellingOrders(), 'closer', 'close', $this->workflowRegistry);
            }
            /**
             * Si le Customer est débloqué, on débloque les commandes associées
             */
            if ($blockerTransitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                $this->updateWorkflowState($object->getSellingOrders(), 'closer', 'unlock', $this->workflowRegistry);
            }
            /**
             * Si le customer est bloqué, on bloque les commandes associées
             */
            if ($blockerTransitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                $sellingOrders = $object->getSellingOrders();
                foreach ($sellingOrders as $sellingOrder) {
                    $sell = $sellingOrder->getState();
                    if ($sell === 'to_validate' || $sell === 'agreed' || $sell === 'partially_delivered') {
                        $this->applyTransitionToWorkflow($sellingOrder, 'closer', 'block', $this->workflowRegistry);
                    }
                }
            }
        }
        /**
         * Dans le cas du workflow blocker de Expedition
         */
        if ($object instanceof Expedition && $blockerWorkflowName === 'blocker') {
            /**
             * Si l'expedition est désactivée, on désactive les items de facture associées
             */
            if ($blockerTransitionName === 'disable' && $before === 'enabled' && $after === 'disabled') {
                $billItems = $object->getExpeditionItems();
                foreach ($billItems as $billItem) {
                    $bill = $billItem->getBill();
                    $blocker = $bill->getEmbBlocker()->getState();
                    if ($blocker === 'blocked') {
                        $this->logger->info(`la facturation est en êtat $state passe directement a close ` . $state);
                        $this->applyTransitionToWorkflow($bill, 'blocker', 'unlock', $this->workflowRegistry);
                    }
                    $this->applyTransitionToWorkflow($bill, 'blocker', 'disable', $this->workflowRegistry);
                }
            }
            /**
             * Si l'expedition est débloquée, on débloque les items de facture associées
             */
            if ($blockerTransitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                $billItems = $object->getExpeditionItems();
                foreach ($billItems as $billItem) {
                    $bill = $billItem->getBill();
                    $this->applyTransitionToWorkflow($bill, 'blocker', 'unlock', $this->workflowRegistry);
                }
            }
            /**
             * Si l'expedition est bloquée, on bloque les items de facture associées
             */
            if ($blockerTransitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                $billItems = $object->getExpeditionItems();
                foreach ($billItems as $billItem) {
                    $bill = $billItem->getBill();
                    $this->applyTransitionToWorkflow($bill, 'blocker', 'block', $this->workflowRegistry);
                }
            }
        }
        /**
         * Dans le cas du workflow blocker de Supplier
         */
        if ($object instanceof Supplier && $blockerWorkflowName === 'blocker') {
            /**
             * Si un fournisseur est désactivé, on désactive les commandes fournisseurs associées
             */
            if ($blockerTransitionName === 'disable' && $before === 'enabled' && $after === 'disabled') {
                $this->updateWorkflowState($object->getOrders(), 'closer', 'unlock', $this->workflowRegistry);
                $this->updateWorkflowState($object->getOrders(), 'closer', 'close', $this->workflowRegistry);
            }
            /**
             * Si le fournisseur est débloqué, on débloque les commandes fournisseurs associées
             */
            if ($blockerTransitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                $purchaseOrders = $object->getOrders();
                foreach ($purchaseOrders as $purchaseOrder) {
                    $embState = $purchaseOrder->getEmbState()->getState();
                    if ($embState === 'agreed' || $embState === 'partially_received') {
                        $this->applyTransitionToWorkflow($purchaseOrder, 'closer', 'unlock', $this->workflowRegistry);
                    }
                }
            }
            /**
             * Si le fournisseur est bloqué, on bloque les commandes fournisseurs associées
             */
            if ($blockerTransitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                $purchaseOrders = $object->getOrders();
                foreach ($purchaseOrders as $purchaseOrder) {
                    $embState = $purchaseOrder->getEmbState()->getState();
                    if ($embState === 'agreed' || $embState === 'partially_received') {
                        $this->applyTransitionToWorkflow($purchaseOrder, 'closer', 'block', $this->workflowRegistry);
                    }
                }
            }
        }
        /**
         * Dans le cas du workflow blocker de Component
         */
        if ($object instanceof Component && $blockerWorkflowName === 'blocker') {
            /**
             * Si un composant est débloqué, on débloque les commandes fournisseurs associées, ainsi que les réceptions associées
             */
            if ($blockerTransitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                //les commandes fournisseurs qui sont en cours passe a enabled si le composant passe a enabled
                $compo = $object->getId();
                $componentItemRepository = $this->entityManager->getRepository(ComponentItem::class);
                $items = $componentItemRepository->findByComponentId($compo);
                foreach ($items as $item) {
                    $state = $item->getEmbState()->getState();
                    if ($state === 'agreed' || $state === 'partially_received') {
                        $this->applyTransitionToWorkflow($item, 'purchase_order_item_closer', 'unlock', $this->workflowRegistry);
                    }
                    //les receipts pas encore réalisées passe a enabled si le composant passe a enabled
                    $receipts = $item->getReceipts();
                    foreach ($receipts as $receipt) {
                        $state_receipt = $receipt->getEmbState()->getState();
                        if ($state_receipt === 'asked' || $state_receipt === 'to_validate') {
                            $this->applyTransitionToWorkflow($receipt, 'blocker', 'unlock', $this->workflowRegistry);
                        }
                    }
                }
            }
            /**
             * Si un composant est bloqué, on bloque les commandes fournisseurs associées, ainsi que les réceptions associées
             */
            if ($blockerTransitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                //les commandes fournisseurs qui sont en cours passe a blocked si le composant passe a blocked
                $compo = $object->getId();
                $componentItemRepository = $this->entityManager->getRepository(ComponentItem::class);
                $items = $componentItemRepository->findBy(['id' => $compo]);
                foreach ($items as $item) {
                    $state = $item->getEmbState()->getState();
                    if ($state === 'agreed' || $state === 'partially_received') {
                        $this->applyTransitionToWorkflow($item, 'purchase_order_item_closer', 'block', $this->workflowRegistry);
                    }
                }
                $references = $object->getReferences();
                $checkRepository = $this->entityManager->getRepository(Check::class);
                foreach ($references as $reference) {
                    $referenceId = $reference->getId();
                    $checks = $checkRepository->findBy(['reference' => $referenceId]);
                    foreach ($checks as $check) {
                        $state_check = $check->getEmbState()->getState();
                        if ($state_check === 'asked') {
                            $this->applyTransitionToWorkflow($check, 'check', 'reject', $this->workflowRegistry);
                        }
                    }
                }
                $preparations = $object->getPreparationComponents();
                foreach ($preparations as $preparation) {
                    $preparation_state = $preparation->getEmbState()->getState();
                    if ($preparation_state === 'asked' || $preparation_state === 'agreed') {
                        $this->applyTransitionToWorkflow($preparation, 'blocker', 'block', $this->workflowRegistry);
                    }
                }
            }
        }
        /**
         * Dans le cas du workflow blocker de Product
         */
        if ($object instanceof Product && $blockerWorkflowName === 'blocker') {
            $orders = $object->getProductOrders();
            $productstate = $object->getEmbState()->getState();
            /**
             * Si le produit est bloqué, on bloque les ordres de fabrication associées
             */
            if ($blockerTransitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                if ($productstate === 'agreed' || $productstate === 'to_validate' || $productstate === 'warning') {
                    foreach ($orders as $order) {
                        $state = $order->getEmbState()->getState();
                        if ($state === 'asked' || $state === 'agreed') {
                            $this->applyTransitionToWorkflow($order, 'closer', 'block', $this->workflowRegistry);
                        }
                    }
                }
            }
            /**
             * Si le produit est débloqué, on débloque les ordres de fabrication associées
             */
            if ($blockerTransitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                if ($productstate === 'agreed' || $productstate === 'to_validate' || $productstate === 'warning') {
                    foreach ($orders as $order) {
                        $state = $order->getEmbState()->getState();
                        if ($state === 'asked' || $state === 'agreed') {
                            $this->applyTransitionToWorkflow($order, 'closer', 'unlock', $this->workflowRegistry);
                        }
                    }
                }
            }
        }
        /**
         * Dans le cas du workflow blocker de Receipt
         */
        if ($object instanceof Receipt && $blockerWorkflowName === 'blocker') {
            $checks = $object->getChecks();
            $state = $object->getEmbState()->getState();
            $block = $object->getEmbBlocker()->getState();
            /**
             * Si la réception est bloquée, on bloque les contrôles associées
             */
            if ($blockerTransitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                if ($state === 'asked' || $state === 'to_validate') {
                    foreach ($checks as $check) {
                        $this->applyTransitionToWorkflow($check, 'check', 'reject', $this->workflowRegistry);
                    }
                }
            }
        }
    }

    /**
     * Cette méthode permet de gérer les transitions du workflow closer des entités SellingOrder, SellingOrderItem, ManufacturingOrder
     * @param Event $event
     * @return void
     */
    public function onWorkflowCloserTransition(Event $event): void
    {
        $object = $event->getSubject();
        $closerTransition = $event->getTransition();
        $closerWorkflowName = $event->getWorkflowName();
        $before = $closerTransition->getFroms()[0];
        $after = $closerTransition->getTos()[0];
        $closerTransitionName = $closerTransition->getName();

        /**
         * Dans le cas du workflow closer de Order
         */
        if ($object instanceof Order && $closerWorkflowName === 'closer') {
            /**
             * Si une commande est désactivée, on désactive les items de commande associées
             */
            if ($closerTransitionName === 'close' && $before === 'enabled' && $after === 'closed') {
                $this->updateWorkflowState($object->getSellingOrderItems(), 'closer', 'unlock', $this->workflowRegistry);
                $this->updateWorkflowState($object->getSellingOrderItems(), 'closer', 'close', $this->workflowRegistry);
            }
            /**
             * Si la commande est débloquée, on débloque les items de commande associées
             */
            if ($closerTransitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                $this->updateWorkflowState($object->getSellingOrderItems(), 'closer', 'unlock', $this->workflowRegistry);
            }
            /**
             * Si la commande est bloquée, on bloque les items de commande associées
             */
            if ($closerTransitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                $sellingorderitems = $object->getSellingOrderItems();
                foreach ($sellingorderitems as $sellingorderitem) {
                    $sellitem = $sellingorderitem->getState();
                    if ($sellitem === 'draft' || $sellitem === 'agreed' || $sellitem === 'partially_delivered') {
                        $this->applyTransitionToWorkflow($sellingorderitem, 'closer', 'block', $this->workflowRegistry);
                    }
                }
            }
        }
        /**
         * Dans le cas du workflow closer de Item
         */
        if ($object instanceof Item && $closerWorkflowName === 'closer') {
            /**
             * Si un item est désactivé, on désactive les expéditions associées
             */
            if ($closerTransitionName === 'close' && $before === 'enabled' && $after === 'closed') {
                // mettre a jours le blocker workflow associée a Expedition
                $this->updateWorkflowState($object->getExpeditions(), 'blocker', 'unlock', $this->workflowRegistry);
                $this->updateWorkflowState($object->getExpeditions(), 'blocker', 'disable', $this->workflowRegistry);
            }

            /**
             * Si l'item est débloqué, on débloque les expéditions associées
             */
            if ($closerTransitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                $this->updateWorkflowState($object->getExpeditions(), 'blocker', 'unlock', $this->workflowRegistry);
            }
            /**
             * Si l'item est bloqué, on bloque les expéditions associées
             */
            if ($closerTransitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                $expeditions = $object->getExpeditions();
                foreach ($expeditions as $expedition) {
                    $state = $expedition->getEmbState()->getState();
                    if ($state === 'draft' || $state === 'to_send' ) {
                        $this->applyTransitionToWorkflow($expedition, 'blocker', 'block', $this->workflowRegistry);
                    }
                }
            }
        }
        /**
         * Dans le cas du workflow closer de PurchaseOrder
         */
        if ($object instanceof PurchaseOrder && $closerWorkflowName === 'closer') {
            /**
             * Si une commande fournisseur est désactivée, on désactive les items de commande fournisseur associées
             */
            if ($closerTransitionName === 'close' && $before === 'enabled' && $after === 'closed') {
                $this->updateWorkflowState($object->getItems(), 'purchase_order_item_closer', 'unlock', $this->workflowRegistry);
                $this->updateWorkflowState($object->getItems(), 'purchase_order_item_closer', 'close', $this->workflowRegistry);
            }
            /**
             * Si la commande fournisseur est débloquée, on débloque les items de commande fournisseur associées
             */
            if ($closerTransitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                $purchaseItems = $object->getItems();
                foreach ($purchaseItems as $purchaseItem) {
                    $state = $purchaseItem->getState();
                    if ($state === 'agreed' || $state === 'partially_received') {
                        $this->applyTransitionToWorkflow($purchaseItem, 'purchase_order_item_closer', 'unlock', $this->workflowRegistry);
                    }
                }
            }
            /**
             * Si la commande fournisseur est bloquée, on bloque les items de commande fournisseur associées
             */
            if ($closerTransitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                $purchaseItems = $object->getItems();
                foreach ($purchaseItems as $purchaseItem) {
                    $state = $purchaseItem->getState();
                    if ($state === 'agreed' || $state === 'partially_received') {
                        $this->applyTransitionToWorkflow($purchaseItem, 'purchase_order_item_closer', 'block', $this->workflowRegistry);
                    }
                }
            }
        }
        /**
         * Dans le cas du workflow closer de ManufacturingOrder
         */
        if ($object instanceof ManufacturingOrder && $closerWorkflowName === 'closer') {
            $operations = $object->getOperationOrders();
            $manufacturingstate = $object->getEmbState()->getState();
            /**
             * Si l'ordre de fabrication est bloqué, on bloque les opérations associées, ainsi que les préparations de composant associées
             */
            if ($closerTransitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                if ($manufacturingstate === 'agreed' || $manufacturingstate === 'asked') {
                    foreach ($operations as $operation) {
                        $state_operation = $operation->getEmbState()->getState();
                        if ($state_operation === 'draft' || $state_operation === 'agreed' || $state_operation === 'warning') {
                            $this->applyTransitionToWorkflow($operation, 'closer', 'block', $this->workflowRegistry);
                        }
                    }
                    $preparations = $object->getPreparationOrders();
                   foreach ($preparations as $preparation) {
                        $state_preparation = $preparation->getEmbState()->getState();
                        if ($state_preparation === 'draft' || $state_preparation === 'agreed') {
                           $this->applyTransitionToWorkflow($preparation, 'blocker', 'block', $this->workflowRegistry);
                        }
                   }
                }
            }
            /**
             * Si l'ordre de fabrication est débloqué, on débloque les opérations associées, ainsi que les préparations de composant associées
             */
            if ($closerTransitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                if ($manufacturingstate === 'agreed' || $manufacturingstate === 'asked') {
                    foreach ($operations as $operation) {
                        $state = $operation->getEmbState()->getState();
                        if ($state === 'draft' || $state === 'agreed' || $state === 'warning') {
                            $this->applyTransitionToWorkflow($operation, 'closer', 'unlock', $this->workflowRegistry);
                        }
                    }
                    $preparations = $object->getPreparationOrders();
                    foreach ($preparations as $preparation) {
                        $state_preparation = $preparation->getEmbState()->getState();
                        if ($state_preparation === 'draft' || $state_preparation === 'agreed') {
                            $this->applyTransitionToWorkflow($preparation, 'blocker', 'unlock', $this->workflowRegistry);
                        }
                    }
                }
            }
        }
    }
    /**
     * Cette méthode permet de gérer les transitions du workflow purchase_order_item_closer de PurchaseOrderItem
     * @param Event $event
     * @return void
     */
    public function onWorkflowPurchaseOrderItemCloserTransition(Event $event): void
    {
        $object = $event->getSubject();
        $transition = $event->getTransition();
        $workflowName = $event->getWorkflowName();
        $before = $transition->getFroms()[0];
        $after = $transition->getTos()[0];
        $transitionName = $transition->getName();
        /**
         * Dans le cas du workflow purchase_order_item_closer de PurchaseOrderItem
         */
        if ($object instanceof PurchaseOrderItem && $workflowName === 'purchase_order_item_closer') {
            /**
             * Si un item de commande fournisseur est désactivé, on désactive les réceptions associées
             */
            if ($transitionName === 'close' && $before === 'enabled' && $after === 'closed') {
                $this->updateWorkflowState($object->getReceipts(), 'blocker', 'unlock', $this->workflowRegistry);
                $this->updateWorkflowState($object->getReceipts(), 'blocker', 'disable', $this->workflowRegistry);
            }
        }
    }

    public function applyTransitionToWorkflow($object, $workflowName, $transitionName, $workflowRegistry): void
    {
        $workflow = $workflowRegistry->get($object, $workflowName);
        if ($workflow->can($object, $transitionName)) {
            $workflow->apply($object, $transitionName);
        }
    }

    public function updateWorkflowState($objects, $workflowName, $transitionName, $workflowRegistry): void
    {
        foreach ($objects as $object) {
            $workflow = $workflowRegistry->get($object, $workflowName);
            if ($workflow->can($object, $transitionName)) {
                $workflow->apply($object, $transitionName);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.blocker.transition' => 'onWorkflowBlockerTransition',
            'workflow.closer.transition' => 'onWorkflowCloserTransition',
            'workflow.purchase_order_item_closer.transition' => 'onWorkflowPurchaseOrderItemCloserTransition'
        ];
    }
}