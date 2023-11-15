<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;
use App\Entity\Selling\Order\Order;
use App\Entity\Selling\Order\Item;
use Psr\Log\LoggerInterface as Logger;

class SellingOrderSubscriber implements EventSubscriberInterface
{
    private $workflowRegistry;

    public function __construct(Registry $workflowRegistry, private Logger $logger)
    {
        $this->workflowRegistry = $workflowRegistry;
    }

    public function onWorkflowSellingOrderTransition(Event $event): void
    {
        $object = $event->getSubject();
        $workflowName = $event->getWorkflowName();
        $transition = $event->getTransition();
        $before = $transition->getFroms()[0];
        $after = $transition->getTos()[0];
        $transitionName = $transition->getName();

        if ($object instanceof Order && $workflowName === 'selling_order') {
            if ($transitionName === 'validate' && $before === 'to_validate' && $after === 'agreed') {
                $this->updateWorkflowState($object->getSellingOrderItems(), 'selling_order_item', 'validate', $this->workflowRegistry);
            }
        }
    }

    public function onWorkflowSellingOrderItemTransition(Event $event): void
    {
        $object = $event->getSubject();
        $workflowName = $event->getWorkflowName();
        $transition = $event->getTransition();
        $before = $transition->getFroms()[0];
        $after = $transition->getTos()[0];
        $transitionName = $transition->getName();

        if ($object instanceof Item && $workflowName === 'selling_order_item') {
            if ($transitionName === 'deliver' && ($before === 'agreed' || $before === 'partially_delivered') && $after === 'delivered') {
                // Récupérer la commande associée
                $order = $object->getOrder();

                if ($order instanceof Order) {
                    $allItemsDelivered = $this->checkAllItemsValid($order, $object, $event);
                }
                // 3. Si tous les autres items sont dans un état 'delivered', passer la commande à l'état 'delivered'
                if ($allItemsDelivered) {
                    $etatorder = $order->getEmbState()->getState();

                    if ($etatorder === 'draft') {
                        $this->logger->info(`la commande est en êtat draft passe directement a delivered ` . $etatorder);
                        $this->applyTransitionToWorkflow($order, 'selling_order', 'submit_validation', $this->workflowRegistry);
                        $this->applyTransitionToWorkflow($order, 'selling_order', 'validate', $this->workflowRegistry);
                    }
                    if ($etatorder === 'to_validate') {
                        $this->logger->info(`la commande est en êtat to_validate passe directement a delivered ` . $etatorder);
                        $this->applyTransitionToWorkflow($order, 'selling_order', 'validate', $this->workflowRegistry);
                    }
                    $this->applyTransitionToWorkflow($order, 'selling_order', 'deliver', $this->workflowRegistry);
                }
            }
            if ($transitionName === 'pay' && $before === 'delivered' && $after === 'paid') {

                $order = $object->getOrder();

                if ($order instanceof Order) {
                    $allItemsPaid = $this->checkAllItemsValid($order, $object, $event);
                }
                // 3. Si tous les autres items sont dans un état 'delivered', passer la commande à l'état 'delivered'
                if ($allItemsPaid) {
                    $etatorder = $order->getEmbState()->getState();

                    if ($etatorder === 'draft') {
                        $this->logger->info(`la commande est en êtat draft passe directement a paid ` . $etatorder);
                        $this->applyTransitionToWorkflow($order, 'selling_order', 'submit_validation', $this->workflowRegistry);
                        $this->applyTransitionToWorkflow($order, 'selling_order', 'validate', $this->workflowRegistry);
                        $this->applyTransitionToWorkflow($order, 'selling_order', 'deliver', $this->workflowRegistry);
                    }
                    if ($etatorder === 'to_validate') {
                        $this->logger->info(`la commande est en êtat to_validate passe directement a paid ` . $etatorder);
                        $this->applyTransitionToWorkflow($order, 'selling_order', 'validate', $this->workflowRegistry);
                        $this->applyTransitionToWorkflow($order, 'selling_order', 'deliver', $this->workflowRegistry);
                    }
                    if (($etatorder === 'agreed') || ($etatorder === 'partially_delivered')) {
                        $this->logger->info(`la commande est en êtat agreed passe directement a paid ` . $etatorder);
                        $this->applyTransitionToWorkflow($order, 'selling_order', 'deliver', $this->workflowRegistry);
                    }
                    $this->applyTransitionToWorkflow($order, 'selling_order', 'pay', $this->workflowRegistry);
                }
            }
        }
    }

    private function checkAllItemsValid(Order $order, Item $currentItem, Event $event): bool
    {
        $otherItems = $order->getSellingOrderItems();
        $allItemsValid = true;
        $transition = $event->getTransition();
        $after = $transition->getTos()[0];
        foreach ($otherItems as $otherItem) {
            if ($otherItem !== $currentItem) {
                $otherItemState = $this->workflowRegistry->get($otherItem, 'selling_order_item')->getMarking($otherItem);
                $otherItemBlock = $this->workflowRegistry->get($otherItem, 'closer')->getMarking($otherItem);

                if ((!$otherItemState->has($after)) || (!$otherItemBlock->has('enabled'))) {
                    $allItemsValid = false;
                    break;
                }
            }
        }

        return $allItemsValid;
    }

    private function applyTransitionToWorkflow($object, $workflowName, $transitionName, $workflowRegistry): void
    {
        $workflow = $workflowRegistry->get($object, $workflowName);

        if ($workflow->can($object, $transitionName)) {
            $workflow->apply($object, $transitionName);
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
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.selling_order.transition' => 'onWorkflowSellingOrderTransition',
            'workflow.selling_order_item.transition' => 'onWorkflowSellingOrderItemTransition',

        ];
    }
}