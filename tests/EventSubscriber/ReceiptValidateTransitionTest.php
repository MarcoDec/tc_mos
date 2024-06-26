<?php

namespace App\Tests\EventSubscriber;

use App\Entity\Logistics\Order\Receipt;
use App\Entity\Management\Unit;
use App\Entity\Purchase\Order\ComponentItem;
use App\Entity\Purchase\Order\Order as PurchaseOrder;
use App\EventSubscriber\PurchaseOrderSubscriber;
use App\Entity\Embeddable\Measure;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReceiptValidateTransitionTest extends KernelTestCase
{
    use WorkflowSubscriberTestTrait;
    private function generateReceiptFromArray(array $data): Receipt
    {
        $purchaseOrderComponentItem = new ComponentItem();
        $componentUnit = new Unit();
        $componentUnit->setCode('ml');
        $componentUnit->setParent(null);
        $componentUnit->setBase(1);

        $receivedQuantity = new Measure();
        $receivedQuantity->setValue($data['item']['receivedQuantity']);
        $receivedQuantity->setUnit($componentUnit);

        $confirmedQuantity = new Measure();
        $confirmedQuantity->setValue($data['item']['confirmedQuantity']);
        $confirmedQuantity->setUnit($componentUnit);

        $purchaseOrderComponentItem
            ->setState($data['item']['state'])
            ->setBlocker($data['item']['blocker'])
            ->setReceivedQuantity($receivedQuantity)
            ->setConfirmedQuantity($confirmedQuantity)
        ;

        $quantityReceived = new Measure();
        $quantityReceived->setValue($data['quantity']);
        $quantityReceived->setUnit($componentUnit);

        $receipt = new Receipt();
        $receipt->setState($data['state']);
        $receipt->setBlocker($data['blocker']);
        $receipt->setQuantity($quantityReceived);
        $receipt->setItem($purchaseOrderComponentItem);
        return $receipt;
    }
    /**
     * @test
     * @testdox  Test de la méthode **`onWorkflowReceiptTransition`** de
     *           la classe **`PurchaseOrderSubscriber`** dans le cas de la
     *           transition **`validate`**
     * @return void
     */
    public function testApplyValidateOnOneShotReceipt(): void {
        $this->setTransitionName('validate');
        //region création d'un objet Receipt bon pour la transition validate
        $receipt1 = $this->generateReceiptFromArray([
            'state' => 'to_validate',
            'blocker' => 'enabled',
            'quantity' => 10,
            'item' => [
                'state' => 'agreed',
                'blocker' => 'enabled',
                'receivedQuantity' => 0,
                'confirmedQuantity' => 10
            ]
        ]);
//        print_r($receipt1);

        //endregion
        /** @var PurchaseOrderSubscriber $subscriber */
        $subscriber = $this->getSubscriber();
        $this->getEventMock()->expects($this->once())->method('getWorkflowName')->willReturn('receipt');
        $this->getEventMock()->expects($this->once())->method('getTransition')->willReturn($this->getTransitionMock());
        $this->getTransitionMock()->expects($this->once())->method('getName')->willReturn('validate');
        $this->getEventMock()->expects($this->once())->method('getSubject')->willReturn($receipt1);
        $subscriber->onWorkflowReceiptTransition($this->getEventMock());
//        $this->assertEquals('closed', $receipt1->getState(), 'Le receipt1 devrait être dans l\'état closed');
        $this->assertEquals('received', $receipt1->getItem()->getState(), 'L\'item de commande du receipt1 devrait être dans l\'état received');
}

    public function testApplyValidateOnLastReceipt(): void {
        $this->setTransitionName('validate');
        $receipt2 = $this->generateReceiptFromArray([
            'state' => 'to_validate',
            'blocker' => 'enabled',
            'quantity' => 10,
            'item' => [
                'state' => 'partially_received',
                'blocker' => 'enabled',
                'receivedQuantity' => 10,
                'confirmedQuantity' => 20
            ]
        ]);
        /** @var PurchaseOrderSubscriber $subscriber */
        $subscriber = $this->getSubscriber();
        $this->getEventMock()->expects($this->once())->method('getSubject')->willReturn($receipt2);
        $this->getEventMock()->expects($this->once())->method('getWorkflowName')->willReturn('receipt');
        $this->getEventMock()->expects($this->once())->method('getTransition')->willReturn($this->getTransitionMock());
        $this->getTransitionMock()->expects($this->once())->method('getName')->willReturn('validate');
        $subscriber->onWorkflowReceiptTransition($this->getEventMock());
//        $this->assertEquals('closed', $receipt2->getState(), 'Le receipt2 devrait être dans l\'état closed');
        $this->assertEquals('received', $receipt2->getItem()->getState(), 'L\'item de commande du receipt2 devrait être dans l\'état partially_received');

    }

    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->setEntity(new Receipt());
        $this->setWorkflowName('receipt');
        $this->traitSetUp();
        $this->setSubscriber(PurchaseOrderSubscriber::class);
    }
}