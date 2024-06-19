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

    /**
     * Cette fonction est appelée lorsqu'une transition est effectuée sur un objet de type Order
     * dans le workflow selling_order.
     * Elle vérifie que l'état actuel de l'objet est to_validate
     * et que la transition effectuée est validate,
     * puis met à jour l'état des items de la commande en conséquence.
     * @param Event $event
     * @return void
     */
    public function onWorkflowSellingOrderTransition(Event $event): void
    {
        $object = $event->getSubject();
        $workflowName = $event->getWorkflowName();
        $transition = $event->getTransition();
        $transitionName = $transition->getName();

        if ($object instanceof Order && $workflowName === 'selling_order') {
            if ($transitionName === 'validate' && $object->getEmbState()->getState() === 'to_validate') {
                $this->updateWorkflowState($object->getSellingOrderItems(), 'selling_order_item', 'draft', 'validate', $this->workflowRegistry);
            }
        }
    }

    /**
     * cette fonction est appelée lorsqu'une transition est effectuée sur un objet de type Item
     * dans le workflow selling_order_item.
     * Elle vérifie l'état actuel de l'objet et la transition effectuée,
     * puis met à jour l'état de la commande associée en conséquence.
     * Si tous les items de la commande sont dans un état delivered,
     * la commande est passée à l'état delivered.
     * Si tous les items de la commande sont dans un état paid,
     * la commande est passée à l'état paid.
     * @param Event $event
     * @return void
     */
    public function onWorkflowSellingOrderItemTransition(Event $event): void
    {
        $object = $event->getSubject();
        $workflowName = $event->getWorkflowName();
        $transition = $event->getTransition();
        $currentObjectState = $object->getEmbState()->getState();
        $transitionName = $transition->getName();

        if ($object instanceof Item && $workflowName === 'selling_order_item') {
            if ($transitionName === 'deliver' && ($currentObjectState === 'agreed' || $currentObjectState === 'partially_delivered')) {
                // Récupérer la commande associée à l'item de commande
                /** @var ?Order $order */
                $order = $object->getParentOrder();
                $allItemsDelivered = !($order === null) && $this->checkAllItemsValid($order, $object, $event, 'delivered');
                // 3. Si tous les autres items sont dans un état 'delivered', passer la commande à l'état 'delivered'
                if ($allItemsDelivered) {
                    $etatorder = $order->getEmbState()->getState();
                    switch ($etatorder) {
                        case 'agreed':
                        case 'partially_delivered':
                            $this->logger->info(`la commande est en êtat draft passe directement a delivered ` . $etatorder);
                            $this->applyTransitionToWorkflow($order, 'selling_order', 'deliver', $this->workflowRegistry);
                            break;
                        default:
                            $this->logger->warning("la commande ".$order->getId()." est en êtat " . $etatorder. " ne peut pas passer a delivered suite au passage à delivered de l'item ".$object->getId() );
                            break;
                    }
                }
            }
            if ($transitionName === 'pay' && $currentObjectState === 'delivered') {
                $order = $object->getParentOrder();
                $allItemsPaid = !($order === null) && $this->checkAllItemsValid($order, $object, $event, 'paid');
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

    /**
     * cette fonction vérifie que tous les autres items de la commande
     * sont dans un état valide pour la transition en cours.
     * Elle prend en paramètre l'objet Order, l'objet Item en cours de traitement,
     * l'objet Event et l'état attendu pour les autres items.
     * Si tous les autres items sont dans un état valide,
     * la fonction renvoie true, sinon elle renvoie false.
     * @param Order  $order
     * @param Item   $currentItem
     * @param Event  $event
     * @param string $itemExpectedState
     * @return bool
     */
    private function checkAllItemsValid(Order $order, Item $currentItem, Event $event, string $itemExpectedState): bool
    {
        $otherItems = $order->getSellingOrderItems();
        $allItemsValid = true;
        $transition = $event->getTransition();
        $after = $transition->getTos()[0];
        foreach ($otherItems as $otherItem) {
            if ($otherItem !== $currentItem) {
                $otherItemState = $this->workflowRegistry->get($otherItem, 'selling_order_item')->getMarking($otherItem);
                $otherItemBlock = $this->workflowRegistry->get($otherItem, 'closer')->getMarking($otherItem);

                if ((!$otherItemState->has($itemExpectedState) && $otherItemBlock->has('enabled')) || ($otherItemBlock->has('blocked'))) {
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

    private function updateWorkflowState($objects, $workflowName, $initialState, $transitionName, $workflowRegistry): void
    {
        foreach ($objects as $object) {
            if ($object->getEmbState()->getState() === $initialState) {
                $this->applyTransitionToWorkflow($object, $workflowName, $transitionName, $workflowRegistry);
            } else {
                $this->logger->info('La transition ' . $transitionName . ' n\'a pas été appliquée à l\'item ' . $object->getId() . ' car son état est ' . $object->getEmbState()->getState());
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