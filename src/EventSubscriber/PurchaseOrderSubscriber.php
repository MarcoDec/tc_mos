<?php

namespace App\EventSubscriber;

use App\Entity\Logistics\Order\Receipt;
use App\Entity\Purchase\Order\Order as PurchaseOrder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;
use Psr\Log\LoggerInterface as Logger;


class PurchaseOrderSubscriber implements EventSubscriberInterface
{
    private $workflowRegistry;


    public function __construct(Registry $workflowRegistry, private Logger $logger)
    {
        $this->workflowRegistry = $workflowRegistry;
    }


    public function onWorkflowPurchaseOrderTransition(Event $event): void
    {
        $object = $event->getSubject();
        $workflowName = $event->getWorkflowName();
        $transition = $event->getTransition();
        $transitionName = $transition->getName();

        if ($object instanceof PurchaseOrder && $workflowName === 'purchase_order') {
            if ($transitionName === 'validate') {
                $this->updateWorkflowState($object->getItems(), 'purchase_order_item', 'validate', $this->workflowRegistry);
            }
            if ($transitionName === 'pay') {
                $items = $object->getItems();
                foreach ($items as $item) {
                    $blocker = $item->getEmbBlocker()->getstate();
                    $state = $item->getEmbState()->getstate();
                    if ($blocker === 'enabled') {
                        if ($state !== 'paid') {
                            $this->applyTransitionToWorkflow($item, 'purchase_order_item', 'pay', $this->workflowRegistry);
                        }
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
            if ($transitionName === 'validate') {
            $quantity_confirmed = $object->getItem()->getConfirmedQuantity()->getValue();
            $quantity_received = $object->getQuantity()->getValue();
            $state=$object->getItem()->getEmbState()->getState();
            if($quantity_received >= $quantity_confirmed) {
                if($state != 'inital' || $state != 'monthly' ) {
                $this->logger->info(`la commande fournisseur est en êtat $state passe directement a received ` . $state);
                $this->applyTransitionToWorkflow($object->getItem(), 'purchase_order_item', 'validate', $this->workflowRegistry);
                $this->applyTransitionToWorkflow($object->getItem(), 'purchase_order_item', 'partially_receive', $this->workflowRegistry);
                }
            $this->applyTransitionToWorkflow($object->getItem(), 'purchase_order_item', 'receive', $this->workflowRegistry); 
            } 
            if($quantity_received < $quantity_confirmed) {
                if($state != 'inital' || $state != 'monthly' ) {
                $this->logger->info(`la commande fournisseur est en êtat $state passe directement a partially_received ` . $state);
                $this->applyTransitionToWorkflow($object->getItem(), 'purchase_order_item', 'validate', $this->workflowRegistry);
            } 
            $this->applyTransitionToWorkflow($object->getItem(), 'purchase_order_item', 'partially_receive', $this->workflowRegistry);

        }          

            }
        }
    }

    private function updateWorkflowState($objects, $workflowName, $transitionName, $workflowRegistry): void
    {
        foreach ($objects as $object) {
            $workflow = $workflowRegistry->get($object, $workflowName);
            if ($workflow->can($object, $transitionName)) {
                $workflow->apply($object, $transitionName);
            }
        }
    }

    private function applyTransitionToWorkflow($object, $workflowName, $transitionName, $workflowRegistry): void
    {
        $workflow = $workflowRegistry->get($object, $workflowName);

        if ($workflow->can($object, $transitionName)) {
            $workflow->apply($object, $transitionName);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.purchase_order.transition' => 'onWorkflowPurchaseOrderTransition',
            'workflow.receipt.transition' => 'onWorkflowReceiptTransition',
            'workflow.receipt.transition' => 'onWorkflowReceiptTransition',

        ];
    }
}
