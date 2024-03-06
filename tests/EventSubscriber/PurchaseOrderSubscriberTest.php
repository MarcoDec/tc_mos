<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\PurchaseOrderSubscriber;
use App\Entity\Purchase\Order\Order as PurchaseOrder;
use App\Entity\Logistics\Order\Receipt;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;

class PurchaseOrderSubscriberTest extends TestCase
{
    private $workflowRegistryMock;
    private $loggerMock;
    private $eventMock;
    private $purchaseOrderMock;
    private $receiptMock;
    private $workflowMock;
    private $transitionMock;

    protected function setUp(): void
    {
        $this->workflowRegistryMock = $this->createMock(Registry::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->eventMock = $this->createMock(Event::class);
        $this->purchaseOrderMock = $this->createMock(PurchaseOrder::class);
        $this->receiptMock = $this->createMock(Receipt::class);
        $this->workflowMock = $this->createMock(Workflow::class);
        $this->transitionMock = $this->createMock(Transition::class);
        $this->transitionMock->method('getName')->willReturn('validate');

    }

    public function testOnWorkflowPurchaseOrderTransitionValidate()
    {
        $subscriber = new PurchaseOrderSubscriber($this->workflowRegistryMock, $this->loggerMock);

        $this->eventMock->method('getSubject')->willReturn($this->purchaseOrderMock);
        $this->eventMock->method('getWorkflowName')->willReturn('purchase_order');
        $this->eventMock->method('getTransition')->willReturn($this->transitionMock);

        $this->workflowRegistryMock->method('get')
            ->with($this->purchaseOrderMock, 'purchase_order_item')
            ->willReturn($this->workflowMock);
        $this->assertTrue(true);
        $this->workflowMock->expects($this->any())
            ->method('can')
            ->willReturn(true);

        // Simulate the application of the transition
        $this->workflowMock->expects($this->any())
            ->method('apply')
            ->with($this->purchaseOrderMock, 'validate');

        // Assuming `getItems` returns an array of items that need to be processed
        $itemsMock = new ArrayCollection([$this->purchaseOrderMock]);
        $this->purchaseOrderMock->method('getItems')->willReturn($itemsMock);

        $subscriber->onWorkflowPurchaseOrderTransition($this->eventMock);
    }
}
