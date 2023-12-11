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
    private $workflowRegistry;
    private $entityManager;

    public function __construct(Registry $workflowRegistry, private Logger $logger, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->workflowRegistry = $workflowRegistry;
    }

    public function onWorkflowBlockerTransition(Event $event): void
    {
        $object = $event->getSubject();
        $workflowName = $event->getWorkflowName();
        $transition = $event->getTransition();
        $before = $transition->getFroms()[0];
        $after = $transition->getTos()[0];
        $transitionName = $transition->getName();

        if ($object instanceof Customer && $workflowName === 'blocker') {
            if ($transitionName === 'disable' && $before === 'enabled' && $after === 'disabled') {
                $block = $object->getBlocker();
                $state = $object->getEmbState()->getState();
                if ($block === 'enabled') {
                    if ($state === 'draft') {
                        $this->logger->info(`custumer est en êtat $state passe directement a close ` . $state);
                        $this->applyTransitionToWorkflow($object, 'customer', 'validate', $this->workflowRegistry);
                    }
                    $this->applyTransitionToWorkflow($object, 'customer', 'close', $this->workflowRegistry);
                }
                // si y a les etats blocked on les passe a enabled  dans le closer du sellingOrder
                $this->updateWorkflowState($object->getSellingOrders(), 'closer', 'unlock', $this->workflowRegistry);
                // on passe de enabled a closed dans le closer du sellingOrder
                $this->updateWorkflowState($object->getSellingOrders(), 'closer', 'close', $this->workflowRegistry);
            }
            if ($transitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                $this->updateWorkflowState($object->getSellingOrders(), 'closer', 'unlock', $this->workflowRegistry);
            }
            if ($transitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                $sellingorders = $object->getSellingOrders();

                foreach ($sellingorders as $sellingorder) {
                    $sell = $sellingorder->getState();
                    if ($sell === 'to-validate' || $sell === 'agreed' || $sell === 'partially_delivered') {
                        $this->applyTransitionToWorkflow($sellingorder, 'closer', 'block', $this->workflowRegistry);
                    }
                }
            }
        }
        if ($object instanceof Expedition && $workflowName === 'blocker') {
            if ($transitionName === 'disable' && $before === 'enabled' && $after === 'disabled') {
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
            if ($transitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                $billItems = $object->getExpeditionItems();
                foreach ($billItems as $billItem) {
                    $bill = $billItem->getBill();
                    $this->applyTransitionToWorkflow($bill, 'blocker', 'unlock', $this->workflowRegistry);
                }
            }
        }
        if ($object instanceof Supplier && $workflowName === 'blocker') {
            if ($transitionName === 'disable' && $before === 'enabled' && $after === 'disabled') {
                $this->updateWorkflowState($object->getOrders(), 'closer', 'unlock', $this->workflowRegistry);
                $this->updateWorkflowState($object->getOrders(), 'closer', 'close', $this->workflowRegistry);
            }
            if ($transitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                $purchaseOrders = $object->getOrders();
                foreach ($purchaseOrders as $purchaseOrder) {
                    $embState = $purchaseOrder->getEmbState()->getState();
                    if ($embState === 'agreed' || $embState === 'partially_received') {
                        $this->applyTransitionToWorkflow($purchaseOrder, 'closer', 'unlock', $this->workflowRegistry);
                    }
                }
            }
            if ($transitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                $purchaseOrders = $object->getOrders();
                foreach ($purchaseOrders as $purchaseOrder) {
                    $embState = $purchaseOrder->getEmbState()->getState();
                    if ($embState === 'agreed' || $embState === 'partially_received') {
                        $this->applyTransitionToWorkflow($purchaseOrder, 'closer', 'block', $this->workflowRegistry);
                    }
                }
            }
        }
        if ($object instanceof Component && $workflowName === 'blocker') {
            if ($transitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
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
            if ($transitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                //les commandes fournisseurs qui sont en cours passe a blocked si le composant passe a blocked
                $compo = $object->getId();
                $componentItemRepository = $this->entityManager->getRepository(ComponentItem::class);
                $items = $componentItemRepository->findByComponentId($compo);
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
        if ($object instanceof Product && $workflowName === 'blocker') {
            $orders = $object->getProductOrders();
            $productstate = $object->getEmbState()->getState();
            if ($transitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                if ($productstate === 'agreed' || $productstate === 'to_validate' || $productstate === 'warning') {
                    foreach ($orders as $order) {
                        $state = $order->getEmbState()->getState();
                        if ($state === 'asked' || $state === 'agreed') {
                            $this->applyTransitionToWorkflow($order, 'closer', 'block', $this->workflowRegistry);
                        }
                    }
                }
            }
            if ($transitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
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
        if ($object instanceof Receipt && $workflowName === 'blocker') {
            $checks = $object->getChecks();
            $state = $object->getEmbState()->getState();
            $block = $object->getEmbBlocker()->getState();

            if ($transitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                if ($state === 'asked' || $state === 'to_validate') {
                    foreach ($checks as $check) {
                        $this->applyTransitionToWorkflow($check, 'check', 'reject', $this->workflowRegistry);
                    }
                }
            }
        }
    }

    public function onWorkflowCloserTransition(Event $event): void
    {
        $object = $event->getSubject();
        $transition = $event->getTransition();
        $workflowName = $event->getWorkflowName();
        $before = $transition->getFroms()[0];
        $after = $transition->getTos()[0];
        $transitionName = $transition->getName();

        if ($object instanceof Order && $workflowName === 'closer') {
            if ($transitionName === 'close' && $before === 'enabled' && $after === 'closed') {
                $this->updateWorkflowState($object->getSellingOrderItems(), 'closer', 'unlock', $this->workflowRegistry);
                $this->updateWorkflowState($object->getSellingOrderItems(), 'closer', 'close', $this->workflowRegistry);
            }
            if ($transitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                $this->updateWorkflowState($object->getSellingOrderItems(), 'closer', 'unlock', $this->workflowRegistry);
            }
            if ($transitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                $sellingorderitems = $object->getSellingOrderItems();
                foreach ($sellingorderitems as $sellingorderitem) {
                    $sellitem = $sellingorderitem->getState();
                    if ($sellitem === 'to-validate' || $sellitem === 'agreed' || $sellitem === 'partially_delivered') {
                        $this->applyTransitionToWorkflow($sellingorderitem, 'closer', 'block', $this->workflowRegistry);
                    }
                }
            }
        }
        if ($object instanceof Item && $workflowName === 'closer') {
            if ($transitionName === 'close' && $before === 'enabled' && $after === 'closed') {
                // mettre a jours le blocker workflow associée a Expedition
                $this->updateWorkflowState($object->getExpeditions(), 'blocker', 'unlock', $this->workflowRegistry);
                $this->updateWorkflowState($object->getExpeditions(), 'blocker', 'disable', $this->workflowRegistry);
            }
            if ($transitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                $this->updateWorkflowState($object->getExpeditions(), 'blocker', 'unlock', $this->workflowRegistry);
            }
        }
        if ($object instanceof PurchaseOrder && $workflowName === 'closer') {
            if ($transitionName === 'close' && $before === 'enabled' && $after === 'closed') {
                $this->updateWorkflowState($object->getItems(), 'purchase_order_item_closer', 'unlock', $this->workflowRegistry);
                $this->updateWorkflowState($object->getItems(), 'purchase_order_item_closer', 'close', $this->workflowRegistry);
            }
            if ($transitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                $purchaseItems = $object->getItems();
                foreach ($purchaseItems as $purchaseItem) {
                    $state = $purchaseItem->getState();
                    if ($state === 'agreed' || $state === 'partially_received') {
                        $this->applyTransitionToWorkflow($purchaseItem, 'purchase_order_item_closer', 'unlock', $this->workflowRegistry);
                    }
                }
            }
            if ($transitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
                $purchaseItems = $object->getItems();
                foreach ($purchaseItems as $purchaseItem) {
                    $state = $purchaseItem->getState();
                    if ($state === 'agreed' || $state === 'partially_received') {
                        $this->applyTransitionToWorkflow($purchaseItem, 'purchase_order_item_closer', 'block', $this->workflowRegistry);
                    }
                }
            }
        }
        if ($object instanceof ManufacturingOrder && $workflowName === 'closer') {
            $operations = $object->getOperationOrders();
            $manufacturingstate = $object->getEmbState()->getState();
            if ($transitionName === 'block' && $before === 'enabled' && $after === 'blocked') {
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
            if ($transitionName === 'unlock' && $before === 'blocked' && $after === 'enabled') {
                if ($manufacturingstate === 'agreed' || $manufacturingstate === 'asked') {
                    foreach ($operations as $operation) {
                        $state = $operation->getEmbState()->getState();
                        if ($state === 'draft' || $state === 'agreed' || $state === 'warning') {
                            $this->applyTransitionToWorkflow($operation, 'closer', 'unlock', $this->workflowRegistry);
                        }
                    }
                }
            }
        }
    }
    public function onWorkflowPurchaseOrderItemCloserTransition(Event $event): void
    {
        $object = $event->getSubject();
        $transition = $event->getTransition();
        $workflowName = $event->getWorkflowName();
        $before = $transition->getFroms()[0];
        $after = $transition->getTos()[0];
        $transitionName = $transition->getName();
        if ($object instanceof PurchaseOrderItem && $workflowName === 'purchase_order_item_closer') {
            if ($transitionName === 'close' && $before === 'enabled' && $after === 'closed') {
                $this->updateWorkflowState($object->getReceipts(), 'blocker', 'unlock', $this->workflowRegistry);
                $this->updateWorkflowState($object->getReceipts(), 'blocker', 'disable', $this->workflowRegistry);
            }
        }
    }
    public function handleBlockerTransition()
    {
        // Code to handle the blocker transition event
        // For simplicity, we'll just return a message
        return 'Handling blocker transition event';
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
            'onWorkflowBlockerTransition' => 'handleBlockerTransition',
            'workflow.blocker.transition' => 'onWorkflowBlockerTransition',
            'workflow.closer.transition' => 'onWorkflowCloserTransition',
            'workflow.purchase_order_item_closer.transition' => 'onWorkflowPurchaseOrderItemCloserTransition',
        ];
    }
}