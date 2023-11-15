<?php

namespace App\EventSubscriber;

use App\Entity\Production\Manufacturing\Expedition;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Selling\Order\Item;
use App\Entity\Selling\Order\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;
use Psr\Log\LoggerInterface as Logger;

class BlockerSubscriber implements EventSubscriberInterface
{
    private $workflowRegistry;

    public function __construct(Registry $workflowRegistry,private Logger $logger)
    {
        $this->workflowRegistry = $workflowRegistry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.blocker.transition' => 'onWorkflowBlockerTransition',
            'workflow.closer.transition' => 'onWorkflowCloserTransition',

        ];
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
                $state=$object->getEmbState()->getState();
                if ($block === 'enabled') {
                    if($state === 'draft'){
                        $this->logger->info(`custumer est en êtat draft passe directement a close ` . $block);
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
                    $this->applyTransitionToWorkflow($bill, 'blocker', 'unlock', $this->workflowRegistry);
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
}